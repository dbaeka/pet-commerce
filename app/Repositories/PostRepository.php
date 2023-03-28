<?php

namespace App\Repositories;

use App\Dtos\Post as PostDto;
use App\Models\Post;

/**
 * @extends BaseCrudRepository<Post, PostDto>
 */
class PostRepository extends BaseCrudRepository
{
}
