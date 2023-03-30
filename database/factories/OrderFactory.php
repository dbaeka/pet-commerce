<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\User;
use App\Values\Address;
use App\Values\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Throwable;

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
                        quantity: rand(1, 200),
                        uuid: $product->uuid,
                        price: fake()->randomFloat(2, 2),
                        product: fake()->sentence(),
                    );
                }
            ),
            'address' => new Address(fake()->streetAddress, fake()->address),
            'delivery_fee' => fake()->randomFloat(2, 0, 4),
            'amount' => function (array $attributes) {
                $products = $attributes['products'];
                if (is_array($products)) {
                    $products = collect($products);
                }
                try {
                    return $products->sum(fn (Product $value) => round($value->price * $value->quantity, 2));
                } catch (Throwable) {
                    return fake()->randomFloat();
                }
            },
            'shipped_at' => fn () => ($order_status->title === 'shipped' ? fake()->date() : null)
        ];
    }
}
