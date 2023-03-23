<?php

namespace App\Providers;

use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
use App\Repositories\JwtTokenRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public array $bindings = [
        JwtTokenRepositoryInterface::class =>  JwtTokenRepository::class
    ];


    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
