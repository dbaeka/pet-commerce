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
 * @implements SupportsPaginationTraitInterface<Model|TModel>
 */
abstract class BaseCrudRepository implements CrudRepositoryInterface, SupportsPaginationTraitInterface
{
    use SupportsPagination;

    protected BaseDto $dto;
    protected Model $model;
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
        /** @var TModel|null $model */
        $model = $this->model::query()->create($data);
        return $model ? $this->dto::make($model->getAttributes()) : null;
    }

    /**
     * @return LengthAwarePaginator<TModel|Model>
     */
    public function getList(): LengthAwarePaginator
    {
        $query = $this->model::query();

        return $this->withPaginate($query);
    }

    public function deleteByUuid(string $uuid): bool
    {
        return $this->forUserByUuid($uuid)->delete();
    }

    /**
     * @param string $uuid
     * @return Builder<TModel>
     */
    private function forUserByUuid(string $uuid): Builder
    {
        return $this->model::query()->where('uuid', $uuid);
    }

    public function findByUuid(string $uuid)
    {
        $model = $this->forUserByUuid($uuid)->first();
        return $model ? $this->dto::make($model->getAttributes()) : null;
    }

    public function updateByUuid(string $uuid, array $data)
    {
        /** @var TModel|null $model */
        $model = $this->forUserByUuid($uuid)->first();
        $updated = $model?->update($data);
        return $updated ? $this->dto::make($model->getAttributes()) : null;
    }
}
