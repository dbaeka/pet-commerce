<?php

namespace App\Repositories;

use App\Dtos\Brand as BrandDto;
use App\Models\Brand;
use App\Repositories\Interfaces\BrandRepositoryContract;

/**
 * @extends BaseCrudRepository<Brand, BrandDto>
 */
class BrandRepository extends BaseCrudRepository implements BrandRepositoryContract
{
}
