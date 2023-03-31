<?php

namespace App\Providers;

use App\Events\TokenUsed;
use App\Events\UserLoggedIn;
use App\Listeners\UpdateLastLoggedIn;
use App\Listeners\UpdateTokenLastUsed;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TokenUsed::class => [UpdateTokenLastUsed::class],
        UserLoggedIn::class => [UpdateLastLoggedIn::class]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
