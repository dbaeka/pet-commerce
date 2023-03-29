<?php

namespace App\Repositories;

use App\Dtos\BaseDto;
use App\Repositories\Interfaces\CrudRepositoryInterface;
use App\Repositories\Traits\SupportsPagination;
use App\Repositories\Traits\SupportsPaginationTraitInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use ReflectionException;

/**
 * @template TModel of Model
 * @template TObj of BaseDto
 * @implements CrudRepositoryInterface<TModel, TObj>
 * @implements SupportsPaginationTraitInterface<TModel|Model>
 */
abstract class BaseCrudRepository implements CrudRepositoryInterface, SupportsPaginationTraitInterface
{
    use SupportsPagination;

    protected BaseDto $dto;
    protected Model $model;
    /** @var array<int, string> */
    protected array $with = [];
    private string $model_class;

    /**
     * @throws ReflectionException
     */
    public function __construct(?string $modelClass = null, ?string $dtoClass = null)
    {
        $this->model_class = $modelClass ?: self::guessModelClass();
        $this->model = app($this->model_class);

        /** @var class-string<TObj> $dto_class */
        $dto_class = $dtoClass ?: self::guessDtoClass();
        $r = new ReflectionClass($dto_class);
        $this->dto = $r->newInstanceWithoutConstructor();
    }

    private static function guessModelClass(): string
    {
        return self::guessClass('Models');
    }

    private static function guessClass(string $location): string
    {
        return preg_replace(
            '/(.+)\\\\Repositories\\\\(.+)Repository$/m',
            '$1\\' . $location . '\\\$2',
            static::class
        ) ?: '';
    }

    private static function guessDtoClass(): string
    {
        return self::guessClass('Dtos');
    }

    public function create(array $data)
    {
        return $this->model::query()->create($data)->load($this->with);
    }

    /**
     * @return LengthAwarePaginator<TModel|Model>
     */
    public function getList(): LengthAwarePaginator
    {
        $query = $this->withRelations($this->model::query());

        return $this->withPaginate($query);
    }

    /**
     * @param Builder<TModel|Model> $query
     * @return Builder<TModel|Model>
     */
    final protected function withRelations(Builder $query): Builder
    {
        if (!empty($this->with)) {
            return $query->with($this->with);
        }
        return $query;
    }


    /**
     * @return LengthAwarePaginator<TModel|Model>
     */
    public function getListForUserUuid(string $uuid): LengthAwarePaginator
    {
        $query = $this->model::query()->whereRelation('user', 'users.uuid', $uuid);
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }

    public function deleteByUuid(string $uuid): bool
    {
        return $this->byUuid($uuid)->delete() > 0;
    }

    /**
     * @param string $uuid
     * @return Builder<TModel>
     */
    final protected function byUuid(string $uuid): Builder
    {
        return $this->model::query()->where('uuid', $uuid);
    }

    public function findByUuid(string $uuid)
    {
        return $this->withRelations($this->byUuid($uuid))->first();
    }

    public function updateByUuid(string $uuid, array $data)
    {
        /** @var TModel|null $model */
        $model = $this->byUuid($uuid)->first();
        $updated = $model?->update($data);
        return $updated ? $model->refresh() : null;
    }
}
