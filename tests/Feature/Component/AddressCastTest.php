<?php

namespace Tests\Feature\Component;

use App\Models\Order;
use App\Values\Address as AddressVO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class AddressCastTest extends TestCase
{
    use RefreshDatabase;

    public function testCastsAddressToAddressValue(): void
    {
        $order = Order::factory()->create([
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
        self::assertSame('one', $order->refresh()->address->shipping);
    }

    public function testFailsCastAddressWhenWrongValueType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Order::factory()->create([
            'address' => [
                "shipping" => fake()->streetAddress(),
                "billing" => fake()->streetAddress()
            ]
        ]);
    }
}
