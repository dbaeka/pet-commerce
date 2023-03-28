<?php

namespace App\Http\Requests\v1\MainPage;

use App\Http\Requests\v1\DefaultPaginationRequest;

class PostListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'posts';
}
