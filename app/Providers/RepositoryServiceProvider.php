<?php

namespace App\Providers;

use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ResetRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
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
        JwtTokenRepositoryInterface::class => JwtTokenRepository::class,
        UserRepositoryInterface::class => UserRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        ResetRepositoryInterface::class => ResetRepository::class,
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
