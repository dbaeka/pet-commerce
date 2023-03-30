<?php

namespace App\Repositories;

use App\Dtos\Category as CategoryDto;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryContract;

/**
 * @extends BaseCrudRepository<Category, CategoryDto>
 */
class CategoryRepository extends BaseCrudRepository implements CategoryRepositoryContract
{
}
