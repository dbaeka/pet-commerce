<?php

namespace App\Listeners;

use App\Events\TokenUsed;
use App\Repositories\Interfaces\JwtTokenRepositoryContract;

class UpdateTokenLastUsed
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly JwtTokenRepositoryContract $jwt_token_repository
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TokenUsed $event): void
    {
        $this->jwt_token_repository->updateTokenLastUsed($event->token_unique_id);
    }
}
