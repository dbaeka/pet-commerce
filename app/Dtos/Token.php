<?php

namespace App\Dtos;

use Carbon\Carbon;

class Token extends BaseDto
{
    /**
     * @param int $user_id
     * @param string $unique_id
     * @param string $token_title
     * @param array<string> $restrictions
     * @param array<string> $permissions
     * @param Carbon|null $expires_at
     */
    public function __construct(
        public int  $user_id,
        public string  $unique_id,
        public string  $token_title = '',
        public array   $restrictions = [],
        public array   $permissions = [],
        public ?Carbon $expires_at = null
    ) {
    }
}
