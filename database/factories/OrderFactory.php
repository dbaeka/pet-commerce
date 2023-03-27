<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\User;
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
        /** @var User $user */
        $user = UserFactory::new()->create();
        /** @var Payment $payment */
        $payment = PaymentFactory::new()->create();
        /** @var OrderStatus $order_status */
        $order_status = OrderStatusFactory::new()->create();
        return [
            'user_uuid' => $user->uuid,
            'order_status_uuid' => $order_status->uuid,
            'payment_uuid' => $payment->uuid,
            'uuid' => fake()->unique()->uuid(),
            'products' => Collection::times(
                rand(1, 4),
                function () {
                    /** @var \App\Models\Product $product */
                    $product = ProductFactory::new()->create();
                    return new Product(
                        $product->uuid,
                        rand(1, 200),
                        fake()->uuid(),
                        fake()->randomFloat(2, 2)
                    );
                }
            ),
            'address' => new Address(fake()->streetAddress, fake()->address),
            'delivery_fee' => fake()->randomFloat(2, 0, 4),
            'amount' => fake()->randomFloat(2, 1, 20000),
        ];
    }
}
