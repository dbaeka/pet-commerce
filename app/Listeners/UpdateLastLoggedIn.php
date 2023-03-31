<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Repositories\Interfaces\UserRepositoryContract;

class UpdateLastLoggedIn
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly UserRepositoryContract $user_repository
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserLoggedIn $event): void
    {
        $this->user_repository->updateLastLogin($event->user_uuid);
    }
}
