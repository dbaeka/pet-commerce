<?php

namespace App\Repositories;

use App\Dtos\OrderStatus as OrderStatusDto;
use App\Models\OrderStatus;
use App\Repositories\Interfaces\OrderStatusRepositoryContract;

/**
 * @extends BaseCrudRepository<OrderStatus, OrderStatusDto>
 */
class OrderStatusRepository extends BaseCrudRepository implements OrderStatusRepositoryContract
{
}
