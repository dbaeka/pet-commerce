<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CrudRepositoryContract;
use App\Repositories\Traits\SupportsPagination;
use App\Repositories\Traits\SupportsPaginationTraitContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use ReflectionException;
use Spatie\LaravelData\Data;

abstract class BaseCrudRepository implements CrudRepositoryContract, SupportsPaginationTraitContract
{
    use SupportsPagination;

    protected Data $dto;
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

        /** @var class-string<Data> $dto_class */
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
        return self::guessClass('DataObjects');
    }

    public function create(array $data): Model|Data|null
    {
        return $this->model::query()->create($data)->load($this->with);
    }

    /**
     * @return LengthAwarePaginator<Model>
     */
    public function getList(): LengthAwarePaginator
    {
        $query = $this->withRelations($this->model::query());

        return $this->withPaginate($query);
    }

    /**
     * @param Builder<Model> $query
     * @return Builder<Model>
     */
    final protected function withRelations(Builder $query): Builder
    {
        if (!empty($this->with)) {
            return $query->with($this->with);
        }
        return $query;
    }


    /**
     * @return LengthAwarePaginator<Model>
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
     * @return Builder<Model>
     */
    final protected function byUuid(string $uuid): Builder
    {
        return $this->model::query()->where('uuid', $uuid);
    }

    public function findByUuid(string $uuid): Model|Data|null
    {
        return $this->withRelations($this->byUuid($uuid))->first();
    }

    public function updateByUuid(string $uuid, array $data): Model|Data|null
    {
        /** @var null $model */
        $model = $this->byUuid($uuid)->first();
        $updated = $model?->update($data);
        return $updated ? $model->refresh() : null;
    }
}
