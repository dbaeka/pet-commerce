<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CrudRepositoryContract;
use App\Repositories\Traits\SupportsPagination;
use App\Repositories\Traits\SupportsPaginationTraitContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

abstract class BaseCrudRepository implements CrudRepositoryContract, SupportsPaginationTraitContract
{
    use SupportsPagination;

    protected string $dto_class;
    protected Model $model;
    /** @var array<int, string> */
    protected array $with = [];
    private string $model_class;

    public function __construct(?string $model_class = null, ?string $dto_class = null)
    {
        $this->model_class = $model_class ?: self::guessModelClass();
        $this->model = app($this->model_class);

        $this->dto_class = $dto_class ?: self::guessDtoClass();
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

    public function create(Data $data): Data|null
    {
        $model = $this->model::query()->create($data->toArray())->load($this->with);
        return $this->buildDto($model);
    }

    private function buildDto(?Model $model): ?Data
    {
        return $this->dto_class::{'optional'}($model);
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

    public function findByUuid(string $uuid): Data|null
    {
        /** @var Model $model */
        $model = $this->withRelations($this->byUuid($uuid))->first();
        return $this->buildDto($model);
    }

    public function updateByUuid(string $uuid, Data $data): Data|null
    {
        /** @var Model|null $model */
        $model = $this->withRelations($this->byUuid($uuid))->first();
        $updated = $model?->update($data->toArray());
        return $updated ? $this->buildDto($model->refresh()) : null;
    }
}
