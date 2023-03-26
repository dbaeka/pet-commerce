<?php

namespace App\Dtos;

class Category extends BaseDto
{
    protected ?int $id = null;
    public string $title = '';
    public string $uuid = '';
    public string $slug = '';
    public string $updated_at = '';
    public string $created_at = '';
}
