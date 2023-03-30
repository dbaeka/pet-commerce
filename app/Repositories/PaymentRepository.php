<?php

namespace App\Repositories;

use App\Repositories\Interfaces\PaymentRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentRepository extends BaseCrudRepository implements PaymentRepositoryContract
{
    public function getListForUserUuid(string $uuid): LengthAwarePaginator
    {
        $query = $this->model::query()->whereRelation('user', 'users.uuid', $uuid);
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }
}
