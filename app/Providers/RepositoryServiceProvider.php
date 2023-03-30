<?php

namespace App\Providers;

use App\Repositories\BrandRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\FileRepository;
use App\Repositories\Interfaces\BrandRepositoryContract;
use App\Repositories\Interfaces\CategoryRepositoryContract;
use App\Repositories\Interfaces\FileRepositoryContract;
use App\Repositories\Interfaces\JwtTokenRepositoryContract;
use App\Repositories\Interfaces\OrderRepositoryContract;
use App\Repositories\Interfaces\OrderStatusRepositoryContract;
use App\Repositories\Interfaces\PaymentRepositoryContract;
use App\Repositories\Interfaces\PostRepositoryContract;
use App\Repositories\Interfaces\ProductRepositoryContract;
use App\Repositories\Interfaces\PromotionRepositoryContract;
use App\Repositories\Interfaces\ResetRepositoryContract;
use App\Repositories\Interfaces\UserRepositoryContract;
use App\Repositories\JwtTokenRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderStatusRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PostRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PromotionRepository;
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
        BrandRepositoryContract::class => BrandRepository::class,
        CategoryRepositoryContract::class => CategoryRepository::class,
        FileRepositoryContract::class => FileRepository::class,
        OrderStatusRepositoryContract::class => OrderStatusRepository::class,
        PaymentRepositoryContract::class => PaymentRepository::class,
        PostRepositoryContract::class => PostRepository::class,
        ProductRepositoryContract::class => ProductRepository::class,
        PromotionRepositoryContract::class => PromotionRepository::class
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
