<?php

namespace App\Dtos;

class OrderStatus extends BaseDto
{
    protected ?int $id = null;
    public string $title = '';
    public string $uuid = '';
    public string $updated_at = '';
    public string $created_at = '';
}
