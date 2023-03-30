<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface PaymentRepositoryContract extends CrudRepositoryContract
{
    /**
     * @return LengthAwarePaginator<Model>
     */
    public function getListForUserUuid(string $uuid): LengthAwarePaginator;
}
