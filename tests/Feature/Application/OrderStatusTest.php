<?php

namespace Application;

use App\Models\OrderStatus;
use App\Models\User;
use Database\Factories\OrderStatusFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Application\ApiTestCase;

class OrderStatusTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testUpdateOrderStatus(): void
    {
        $endpoint = self::PREFIX . 'order-statuses/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var OrderStatus $order_status */
        $order_status = OrderStatusFactory::new()->create([
            'title' => 'infinity war'
        ]);

        $data = [
            "title" => 'secret wars',
        ];

        self::assertNotSame($order_status->title, $data['title']);

        $this->putAs($endpoint . $order_status->uuid, $data, $user)
            ->assertOk();

        $order_status->refresh();

        self::assertSame($order_status->title, $data['title']);
    }

    public function testDeleteOrderStatus(): void
    {
        $endpoint = self::PREFIX . 'order-statuses/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var OrderStatus $order_status */
        $order_status = OrderStatusFactory::new()->create();

        self::assertDatabaseHas('order_statuses', [
            'uuid' => $order_status->uuid
        ]);

        $this->deleteAs($endpoint . $order_status->uuid, [], $user)
            ->assertNoContent();

        self::assertDatabaseMissing('order_statuses', [
            'uuid' => $order_status->uuid
        ]);
    }

    public function testFetchOrderStatus(): void
    {
        $endpoint = self::PREFIX . 'order-statuses/';

        /** @var OrderStatus $order_status */
        $order_status = OrderStatusFactory::new()->create();

        $this->get($endpoint . $order_status->uuid)
            ->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 1,
                'uuid' => $order_status->uuid
            ]);
    }

    public function testCreateOrderStatus(): void
    {
        $endpoint = self::PREFIX . 'order-statuses';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        $data = [
            "title" => 'foobar baz',
        ];

        $response = $this->postAs($endpoint, $data, $user);

        $response->assertCreated()
            ->assertJsonStructure($this->mergeDefaultFields(
                "uuid",
                "title"
            ))
            ->assertJsonFragment([
                'success' => 1,
                'title' => 'foobar baz'
            ]);

        self::assertDatabaseHas('order_statuses', [
            'uuid' => $response->json('data.uuid'),
        ]);

        $this->post($endpoint, [
            'email' => 'regular@test.com',
            'password' => 'secret',
        ])->assertUnprocessable()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }

    public function testGetOrderStatusList(): void
    {
        $endpoint = self::PREFIX . 'order-statuses';

        OrderStatusFactory::new()->count(40)->create();

        $this->get($endpoint)
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['title', 'uuid']]
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(20, 'data');

        $this->get($endpoint . '?limit=10')
            ->assertOk()
            ->assertJsonCount(10, 'data');

        $this->get($endpoint . '?page=2&limit=30')
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
            ]);
    }
}
