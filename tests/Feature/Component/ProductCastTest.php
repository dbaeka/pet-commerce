<?php

namespace Tests\Feature\Component;

use App\Models\Order;
use App\Values\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ProductCastTest extends TestCase
{
    use RefreshDatabase;

    public function testCastsProductsToCollectionOfProductValues(): void
    {
        $order = Order::factory()->create([
            'products' => [
                new Product('foo', 20)
            ],
        ]);

        self::assertInstanceOf(Product::class, $order->products->first());
        self::assertSame('foo', $order->products->first()->product);
        self::assertSame(20, $order->products->first()->quantity);

        $products = collect([new Product(
            'one',
            50
        )]);
        $order->products = $products;
        $order->save();

        $order = $order->refresh();
        self::assertSame('one', $order->products->firstOrFail()->product);
    }

    public function testFailsCastProductsWhenWrongValueType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Order::factory()->create([
            'products' => [
                "product" => fake()->streetAddress(),
                "quantity" => fake()->randomNumber()
            ]
        ]);
    }
}
