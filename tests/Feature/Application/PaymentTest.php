<?php

namespace Application;

use App\Enums\PaymentType;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Database\Factories\OrderFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\Feature\Application\ApiTestCase;

class PaymentTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testAdminUpdatePayment(): void
    {
        $endpoint = self::PREFIX . 'payments/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Payment $payment */
        $payment = PaymentFactory::new()->create([
            'type' => PaymentType::CREDIT_CARD
        ]);

        OrderFactory::new()->create([
            'user_uuid' => $user->uuid,
            'payment_uuid' => $payment->uuid
        ]);

        $data = [
            'type' => PaymentType::BANK_TRANSFER,
            'details' => PaymentFactory::getSampleDetail(PaymentType::BANK_TRANSFER)
        ];

        self::assertNotSame($payment->type, $data['type']);

        $this->putAs($endpoint . $payment->uuid, $data, $user)
            ->assertForbidden();

        $payment->refresh();

        self::assertNotSame($payment->type, $data['type']);
        self::assertNotSame($payment->details, $data['details']);
    }

    public function testUserUpdatePayment(): void
    {
        $endpoint = self::PREFIX . 'payments/';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        /** @var Payment $payment */
        $payment = PaymentFactory::new()->create([
            'type' => PaymentType::CREDIT_CARD
        ]);

        OrderFactory::new()->create([
            'user_uuid' => $user->uuid,
            'payment_uuid' => $payment->uuid
        ]);

        $data = [
            'type' => PaymentType::BANK_TRANSFER,
            'details' => PaymentFactory::getSampleDetail(PaymentType::BANK_TRANSFER)
        ];

        self::assertNotEquals($payment->type, $data['type']);

        $this->putAs($endpoint . $payment->uuid, $data, $user)
            ->assertOk();

        $payment->refresh();

        self::assertEquals($payment->type, $data['type']);
        self::assertEquals($payment->details->toArray(), $data['details']);

        $data = [
            'type' => PaymentType::CREDIT_CARD,
            'details' => PaymentFactory::getSampleDetail(PaymentType::BANK_TRANSFER)
        ];

        self::assertNotEquals($payment->type->value, $data['type']->value);

        $this->putAs($endpoint . $payment->uuid, $data, $user)
            ->assertUnprocessable();

        /** @var Order $order */
        $order = OrderFactory::new()->create();
        /** @var Payment $payment */
        $payment = $order->payment()->firstOrFail();

        self::assertNotSame($user->uuid, $order->user_uuid);

        $this->putAs($endpoint . $payment->uuid, $data, $user)
            ->assertForbidden();
    }

    public function testAdminDeletePayment(): void
    {
        $endpoint = self::PREFIX . 'payments/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create();

        /** @var Payment $payment */
        $payment = $order->payment()->firstOrFail();

        self::assertDatabaseHas('payments', [
            'uuid' => $payment->uuid
        ]);

        $this->deleteAs($endpoint . $payment->uuid, [], $user)
            ->assertForbidden();

        self::assertDatabaseHas('payments', [
            'uuid' => $payment->uuid
        ]);
    }

    public function testUserDeletePayment(): void
    {
        $endpoint = self::PREFIX . 'payments/';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create();

        /** @var Payment $payment */
        $payment = $order->payment()->firstOrFail();

        self::assertNotSame($user->uuid, $order->user_uuid);

        self::assertDatabaseHas('payments', [
            'uuid' => $payment->uuid
        ]);

        $this->deleteAs($endpoint . $payment->uuid, [], $user)
            ->assertForbidden();

        self::assertDatabaseHas('payments', [
            'uuid' => $payment->uuid
        ]);

        /** @var Order $order */
        $order = OrderFactory::new()->create([
            'user_uuid' => $user->uuid
        ]);

        /** @var Payment $payment */
        $payment = $order->payment()->firstOrFail();

        self::assertSame($user->uuid, $order->user_uuid);

        self::assertDatabaseHas('payments', [
            'uuid' => $payment->uuid
        ]);

        $this->deleteAs($endpoint . $payment->uuid, [], $user)
            ->assertNoContent();

        self::assertDatabaseMissing('payments', [
            'uuid' => $payment->uuid
        ]);
    }

    public function testAdminFetchPayment(): void
    {
        $endpoint = self::PREFIX . 'payments/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create();

        /** @var Payment $payment */
        $payment = $order->payment()->firstOrFail();

        self::assertNotSame($user->uuid, $order->user_uuid);

        $this->getAs($endpoint . $payment->uuid, $user)
            ->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 1,
                'uuid' => $payment->uuid
            ]);
    }

    public function testUserFetchPayment(): void
    {
        $endpoint = self::PREFIX . 'payments/';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        /** @var Order $order */
        $order = OrderFactory::new()->create();

        /** @var Payment $payment */
        $payment = $order->payment()->firstOrFail();

        self::assertNotSame($user->uuid, $order->user_uuid);

        $this->getAs($endpoint . $payment->uuid, $user)
            ->assertForbidden()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0,
            ]);

        /** @var Order $order */
        $order = OrderFactory::new()->create([
            'user_uuid' => $user->uuid
        ]);

        /** @var Payment $payment */
        $payment = $order->payment()->firstOrFail();

        self::assertSame($user->uuid, $order->user_uuid);

        $this->getAs($endpoint . $payment->uuid, $user)
            ->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 1,
                'uuid' => $payment->uuid
            ]);
    }

    public function testAdminCreatePayment(): void
    {
        $endpoint = self::PREFIX . 'payments';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        $data = [
            'type' => PaymentType::BANK_TRANSFER,
            'details' => PaymentFactory::getSampleDetail(PaymentType::BANK_TRANSFER)
        ];

        $this->postAs($endpoint, $data, $user)
            ->assertForbidden();

        $this->post($endpoint, [
            'type' => 'regular@test.com',
            'details' => 'secret',
        ])->assertForbidden();
    }

    public function testUserCreatePayment(): void
    {
        $endpoint = self::PREFIX . 'payments';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        $data = [
            'type' => PaymentType::BANK_TRANSFER,
            'details' => PaymentFactory::getSampleDetail(PaymentType::BANK_TRANSFER)
        ];

        $response = $this->postAs($endpoint, $data, $user);

        $response->assertCreated()
            ->assertJsonStructure($this->mergeDefaultFields(
                "uuid",
                "details",
                "type"
            ))
            ->assertJsonFragment([
                'success' => 1,
                'type' => PaymentType::BANK_TRANSFER
            ]);

        self::assertDatabaseHas('payments', [
            'uuid' => $response->json('data.uuid'),
        ]);

        $this->post($endpoint, [
            'type' => 'regular@test.com',
            'details' => 'secret',
        ])->assertUnprocessable()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }

    public function testAdminGetPaymentList(): void
    {
        $endpoint = self::PREFIX . 'payments';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Order $orders */
        $orders = OrderFactory::new()->count(40)->create();

        /** @var Collection<string, Payment> $payments */
        $payments = $orders->load(['payment.user'])->pluck('payment');

        // User uuids of payment owners
        $user_uuids = $payments->pluck('user.uuid')->toArray();

        // Check if the payments belong to different users
        // Here, admin can see for different users
        self::assertFalse(count(array_unique($user_uuids)) === 1);

        $this->getAs($endpoint . '?limit=60', $user)
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['type', 'uuid']]
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(40, 'data');

        $this->getAs($endpoint . '?limit=10', $user)
            ->assertOk()
            ->assertJsonCount(10, 'data');

        $this->getAs($endpoint . '?page=2&limit=30', $user)
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
            ]);
    }

    public function testUserGetPaymentList(): void
    {
        $endpoint = self::PREFIX . 'payments';

        /** @var User $user */
        $user = UserFactory::new()->regular()->create();

        /** @var Order $orders */
        // Orders here are created by entirely different users than our client user
        $orders = OrderFactory::new()->count(40)->create([
            'user_uuid' => function () {
                /** @var User $user */
                $user = UserFactory::new()->create();
                return $user->uuid;
            }
        ]);

        /** @var Collection<string, Payment> $payments */
        $payments = $orders->load(['payment.user'])->pluck('payment');

        // User uuids of payment owners
        $user_uuids = $payments->pluck('user.uuid')->toArray();

        // Check if the payments belong to different users
        // Here, client user does not own any payment
        self::assertFalse(in_array($user->uuid, $user_uuids));

        $this->getAs($endpoint . '?limit=60', $user)
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['type', 'uuid']]
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(0, 'data');

        OrderFactory::new()->count(13)->create([
            'user_uuid' => $user->uuid
        ]);

        $this->getAs($endpoint . '?limit=10', $user)
            ->assertOk()
            ->assertJsonCount(10, 'data');

        $this->getAs($endpoint . '?page=2&limit=10', $user)
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
            ])
            ->assertJsonCount(3, 'data');
    }
}
