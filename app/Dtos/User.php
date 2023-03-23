<?php

namespace App\Dtos;

class User extends BaseDto
{
    public function __construct(
        public int    $id,
        public string $uuid,
        public bool   $is_admin = false
    )
    {
    }
}
