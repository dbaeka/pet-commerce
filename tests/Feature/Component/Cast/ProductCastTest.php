<?php

namespace Tests\Feature\Component\Cast;

use App\Models\Order;
use App\Values\Product;
use Database\Factories\OrderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ProductCastTest extends TestCase
{
    use RefreshDatabase;

    public function testCastsProductsToCollectionOfProductValues(): void
    {
        /** @var Order $order */
        $order = OrderFactory::new()->create([
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
        OrderFactory::new()->create([
            'products' => [
                "product" => fake()->streetAddress(),
                "quantity" => fake()->randomNumber()
            ]
        ]);
    }
}
