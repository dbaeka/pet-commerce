<?php

namespace App\Providers;

use App\Repositories\Interfaces\JwtTokenRepositoryContract;
use App\Repositories\Interfaces\OrderRepositoryContract;
use App\Repositories\Interfaces\ResetRepositoryContract;
use App\Repositories\Interfaces\UserRepositoryContract;
use App\Repositories\JwtTokenRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ResetRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All the container bindings that should be registered.
     *
     * @var array<string, string>
     */
    public array $bindings = [
        JwtTokenRepositoryContract::class => JwtTokenRepository::class,
        UserRepositoryContract::class => UserRepository::class,
        OrderRepositoryContract::class => OrderRepository::class,
        ResetRepositoryContract::class => ResetRepository::class,
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
