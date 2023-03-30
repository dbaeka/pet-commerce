<?php

namespace Application;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\OrderFactory;
use Database\Factories\OrderStatusFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ProductFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Application\ApiTestCase;

class OrderTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testAdminUpdateOrder(): void
    {
        $endpoint = self::PREFIX . 'orders/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create([
            'user_uuid' => $user->uuid
        ]);

        /** @var OrderStatus $order_status */
        $order_status = OrderStatusFactory::new()->create();

        $data = [
            'order_status_uuid' => $order_status->uuid,
            'payment_uuid' => $order->payment_uuid,
            'address' => $order->address,
            'products' => $order->products
        ];

        self::assertNotSame($order->order_status_uuid, $data['order_status_uuid']);

        $this->putAs($endpoint . $order->uuid, $data, $user)
            ->assertForbidden();

        $order->refresh();

        self::assertNotSame($order->order_status_uuid, $data['order_status_uuid']);
    }

    public function testUserUpdateOrder(): void
    {
        $endpoint = self::PREFIX . 'orders/';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create([
            'user_uuid' => $user->uuid
        ]);

        /** @var OrderStatus $order_status */
        $order_status = OrderStatusFactory::new()->create();

        $data = [
            'order_status_uuid' => $order_status->uuid,
            'payment_uuid' => $order->payment_uuid,
            'address' => $order->address,
            'products' => $order->products
        ];

        self::assertNotSame($order->order_status_uuid, $data['order_status_uuid']);

        $this->putAs($endpoint . $order->uuid, $data, $user)
            ->assertOk();

        $order->refresh();

        self::assertSame($order->order_status_uuid, $data['order_status_uuid']);
    }

    public function testAdminDeleteOrder(): void
    {
        $endpoint = self::PREFIX . 'orders/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create();

        self::assertDatabaseHas('orders', [
            'uuid' => $order->uuid
        ]);

        $this->deleteAs($endpoint . $order->uuid, [], $user)
            ->assertNoContent();

        self::assertDatabaseMissing('orders', [
            'uuid' => $order->uuid
        ]);
    }

    public function testUserDeleteOrder(): void
    {
        $endpoint = self::PREFIX . 'orders/';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create([
            'user_uuid' => $user->uuid
        ]);

        self::assertDatabaseHas('orders', [
            'uuid' => $order->uuid
        ]);

        $this->deleteAs($endpoint . $order->uuid, [], $user)
            ->assertForbidden();

        self::assertDatabaseHas('orders', [
            'uuid' => $order->uuid
        ]);
    }

    public function testUserFetchOrder(): void
    {
        $endpoint = self::PREFIX . 'orders/';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create([
            'user_uuid' => $user->uuid
        ]);

        $this->getAs($endpoint . $order->uuid, $user)
            ->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields('amount', 'user', 'payment', 'order_status'))
            ->assertJsonFragment([
                'success' => 1,
                'uuid' => $order->uuid
            ]);

        /** @var Order $order */
        $order = OrderFactory::new()->create();

        $this->getAs($endpoint . $order->uuid, $user)
            ->assertForbidden();
    }

    public function testAdminFetchOrder(): void
    {
        $endpoint = self::PREFIX . 'orders/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create();

        $this->getAs($endpoint . $order->uuid, $user)
            ->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields('amount', 'user', 'payment', 'order_status'))
            ->assertJsonFragment([
                'success' => 1,
                'uuid' => $order->uuid
            ]);
    }

    public function testUserCreateOrder(): void
    {
        $endpoint = self::PREFIX . 'orders';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        /** @var OrderStatus $order_status */
        $order_status = OrderStatusFactory::new()->create();

        /** @var Payment $payment */
        $payment = PaymentFactory::new()->create();

        $product_uuid = function () {
            /** @var \App\Models\Product $product */
            $product = ProductFactory::new()->create();
            return $product->uuid;
        };

        $data = [
            'order_status_uuid' => $order_status->uuid,
            'payment_uuid' => $payment->uuid,
            'address' => [
                'shipping' => fake()->streetAddress(),
                'billing' => fake()->streetAddress()
            ],
            'products' => [
                 ['quantity' => 10, 'uuid' => $product_uuid()],
                ['quantity' => 200, 'uuid' => $product_uuid()]]
        ];

        $response = $this->postAs($endpoint, $data, $user);

        $response->assertCreated()
            ->assertJsonStructure($this->mergeDefaultFields(
                "uuid",
                "payment",
                "products"
            ))
            ->assertJsonFragment([
                'success' => 1,
            ]);

        self::assertDatabaseHas('orders', [
            'uuid' => $response->json('data.uuid'),
        ]);

        $this->post($endpoint, [
            'products' => 'regular@test.com',
            'payment_uuid' => 'secret',
        ])->assertUnprocessable()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }

    public function testAdminCreateOrder(): void
    {
        $endpoint = self::PREFIX . 'orders';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var OrderStatus $order_status */
        $order_status = OrderStatusFactory::new()->create();

        /** @var Payment $payment */
        $payment = PaymentFactory::new()->create();

        $product_uuid = function () {
            /** @var \App\Models\Product $product */
            $product = ProductFactory::new()->create();
            return $product->uuid;
        };

        $data = [
            'order_status_uuid' => $order_status->uuid,
            'payment_uuid' => $payment->uuid,
            'address' => [
                'shipping' => fake()->streetAddress(),
                'billing' => fake()->streetAddress()
            ],
            'products' => [
                ['quantity' => 10, 'uuid' => $product_uuid()],
                ['quantity' => 200, 'uuid' => $product_uuid()]]
        ];

        $this->postAs($endpoint, $data, $user)
            ->assertForbidden();
    }

    public function testUserGetOrderList(): void
    {
        $endpoint = self::PREFIX . 'orders';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        OrderFactory::new()->count(6)->create([
            'user_uuid' => $user->uuid
        ]);


        OrderFactory::new()->count(5)->create();

        $this->getAs($endpoint, $user)
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['products', 'uuid']]
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(6, 'data');

        $this->getAs($endpoint . '?limit=10', $user)
            ->assertOk()
            ->assertJsonCount(6, 'data');

        $this->getAs($endpoint . '?page=2&limit=2', $user)
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
            ]);
    }

    public function testAdminGetOrderList(): void
    {
        $endpoint = self::PREFIX . 'orders';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        $orders = OrderFactory::new()->count(6)->create();

        $user_uuids = $orders->pluck('user_uuid')->toArray();

        // Check if the orders belong to different users
        // Here, admin can see for different users
        self::assertFalse(count(array_unique($user_uuids)) === 1);

        $this->getAs($endpoint, $user)
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['products', 'uuid']]
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(6, 'data');

        $this->getAs($endpoint . '?limit=10', $user)
            ->assertOk()
            ->assertJsonCount(6, 'data');

        $this->getAs($endpoint . '?page=2&limit=2', $user)
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
            ]);
    }

    public function testUserGetDashboardList(): void
    {
        $endpoint = self::PREFIX . 'orders/dashboard';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        OrderFactory::new()->count(6)->create();

        $this->getAs($endpoint, $user)
            ->assertForbidden();
    }

    public function testAdminGetDashboardList(): void
    {
        $endpoint = self::PREFIX . 'orders/dashboard';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        Carbon::setTestNow('2020-02-20');

        $this->createOrderTimeRange();

        $this->getAs($endpoint, $user)
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['products', 'uuid']]
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(13, 'data');

        $this->getAs($endpoint . '?fixed_range=today', $user)
            ->assertOk()
            ->assertJsonCount(3, 'data');

        $this->getAs($endpoint . '?fixed_range=monthly', $user)
            ->assertOk()
            ->assertJsonCount(7, 'data');

        $this->getAs($endpoint . '?fixed_range=yearly', $user)
            ->assertOk()
            ->assertJsonCount(13, 'data');

        $this->getAs($endpoint . '?date_range[from]=2020-01-20&date_range[to]=2020-03-20', $user)
            ->assertOk()
            ->assertJsonCount(7, 'data');
    }

    /**
     * @param array<string, mixed> $attributes
     * @return void
     */
    private function createOrderTimeRange(array $attributes = []): void
    {
        OrderFactory::new()->count(6)->create([
            'created_at' => '01-01-2020',
            ...$attributes
        ]);

        OrderFactory::new()->count(3)->create([
            'created_at' => '20-02-2020',
            ...$attributes
        ]);

        OrderFactory::new()->count(4)->create([
            'created_at' => '01-02-2020',
            ...$attributes
        ]);
    }

    public function testUserGetShipmentLocatorList(): void
    {
        $endpoint = self::PREFIX . 'orders/shipment-locator';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        OrderFactory::new()->count(6)->create();

        $this->getAs($endpoint, $user)
            ->assertForbidden();
    }

    public function testAdminGetGetShipmentLocatorList(): void
    {
        $endpoint = self::PREFIX . 'orders/shipment-locator';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        Carbon::setTestNow('2020-02-20');

        $added_definition = [
            'shipped_at' => fake()->date()
        ];
        $this->createOrderTimeRange($added_definition);

        $this->getAs($endpoint, $user)
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['products', 'uuid']]
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(13, 'data');

        $this->getAs($endpoint . '?fixed_range=today', $user)
            ->assertOk()
            ->assertJsonCount(3, 'data');

        $this->getAs($endpoint . '?fixed_range=monthly', $user)
            ->assertOk()
            ->assertJsonCount(7, 'data');

        $this->getAs($endpoint . '?fixed_range=yearly', $user)
            ->assertOk()
            ->assertJsonCount(13, 'data');

        $this->getAs($endpoint . '?date_range[from]=2020-01-20&date_range[to]=2020-03-20', $user)
            ->assertOk()
            ->assertJsonCount(7, 'data');

        /** @var Order $order */
        $order = OrderFactory::new()->create([
            'user_uuid' => $user->uuid,
            'shipped_at' => fake()->date()
        ]);

        $this->getAs($endpoint . '?uuid=' . $order->uuid, $user)
            ->assertOk()
            ->assertJsonCount(1, 'data');


        $this->getAs($endpoint . '?user_uuid=' . $user->uuid, $user)
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function testInvoiceDownload(): void
    {
        $endpoint = self::PREFIX . 'orders/{uuid}/download';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create([
            'shipped_at' => fake()->date()
        ]);

        $endpoint = str_replace('{uuid}', $order->uuid, $endpoint);

        $response = $this->getAs($endpoint, $user);
        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload();
    }
}
