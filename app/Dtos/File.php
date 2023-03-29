<?php

namespace App\Dtos;

class File extends BaseDto
{
    public string $path = '';
    public string $uuid = '';
    public string $name = '';
    public string $size = '';
    public string $type = '';
    public string $updated_at = '';
    public string $created_at = '';
}
