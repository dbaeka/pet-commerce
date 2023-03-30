<?php

namespace App\Repositories;

use App\Dtos\Post as PostDto;
use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryContract;

/**
 * @extends BaseCrudRepository<Post, PostDto>
 */
class PostRepository extends BaseCrudRepository implements PostRepositoryContract
{
}
