<?php

namespace App\Repositories;

use App\Dtos\OrderStatus as OrderStatusDto;
use App\Models\OrderStatus;

/**
 * @extends BaseCrudRepository<OrderStatus, OrderStatusDto>
 */
class OrderStatusRepository extends BaseCrudRepository
{
}
