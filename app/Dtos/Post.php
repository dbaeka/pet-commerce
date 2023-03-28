<?php

namespace App\Dtos;

class Post extends BaseDto
{
    public string $title = '';
    public string $uuid = '';
    public string $slug = '';
    public string $content = '';
    /** @var array<string, string>|null  */
    public ?array $metadata = null;
    public string $updated_at = '';
    public string $created_at = '';
}
