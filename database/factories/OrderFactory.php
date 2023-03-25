<?php

namespace Database\Factories;

use App\Values\Address;
use App\Values\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new(),
            'order_status_id' => fn () => OrderStatusFactory::new(),
            'payment_id' => PaymentFactory::new(),
            'uuid' => fake()->unique()->uuid(),
            'products' => Collection::times(
                rand(1, 4),
                function () {
                    /** @var \App\Models\Product $product */
                    $product = ProductFactory::new()->create();
                    return new Product($product->uuid, rand(1, 200));
                }
            ),
            'address' => new Address(fake()->streetAddress, fake()->address),
            'delivery_fee' => fake()->randomFloat(2, 0, 4),
            'amount' => fake()->randomFloat(2, 1, 20000),
        ];
    }
}
