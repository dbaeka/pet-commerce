<?php

namespace App\Repositories;

use App\Dtos\Product as ProductDto;
use App\Models\Product;

/**
 * @extends BaseCrudRepository<Product, ProductDto>
 */
class ProductRepository extends BaseCrudRepository
{
    protected array $with = ['category', 'brand'];
}
