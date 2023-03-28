<?php

namespace Tests\Feature\Component\Cast;

use App\Models\Order;
use App\Values\Address as AddressVO;
use Database\Factories\OrderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class AddressCastTest extends TestCase
{
    use RefreshDatabase;

    public function testCastsAddressToAddressValue(): void
    {
        /** @var Order $order */
        $order = OrderFactory::new()->create([
            'address' => new AddressVO(
                'foo',
                'bar'
            ),
        ]);

        self::assertInstanceOf(AddressVO::class, $order->address);
        self::assertSame('foo', $order->address->shipping);
        self::assertSame('bar', $order->address->billing);

        $address2 = new AddressVO(
            'one',
            'two'
        );
        $order->address = $address2;
        $order->save();
        $order->refresh();
        self::assertSame('one', $order->address->shipping);
    }

    public function testFailsCastAddressWhenWrongValueType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        OrderFactory::new()->create([
            'address' => [
                "shipping" => fake()->streetAddress(),
                "billing" => fake()->streetAddress()
            ]
        ]);
    }
}
