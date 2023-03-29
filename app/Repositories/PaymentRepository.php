<?php

namespace App\Repositories;

use App\Dtos\Payment as PaymentDto;
use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseCrudRepository<Payment, PaymentDto>
 */
class PaymentRepository extends BaseCrudRepository
{
    /**
     * @return LengthAwarePaginator<Payment|Model>
     */
    public function getListForUserUuid(string $uuid): LengthAwarePaginator
    {
        $query = $this->model::query()->whereRelation('user', 'users.uuid', $uuid);
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }
}