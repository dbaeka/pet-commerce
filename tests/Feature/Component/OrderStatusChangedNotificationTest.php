<?php

namespace Component;

use App\Models\Order;
use App\Models\OrderStatus;
use Database\Factories\OrderFactory;
use Database\Factories\OrderStatusFactory;
use Dbaeka\MsNotification\Events\OrderStatusChanged;
use Dbaeka\MsNotification\Listeners\NotifyOrderStatusUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderStatusChangedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function testOrderStatusChangeNotificationSent(): void
    {
        Event::fake();
        /** @var Order $order */
        $order = OrderFactory::new()->create();
        /** @var OrderStatus $order_status */
        $order_status = OrderStatusFactory::new()->create();

        $order->update([
            'order_status_uuid' => $order_status->uuid
        ]);
        Event::assertListening(OrderStatusChanged::class, NotifyOrderStatusUpdate::class);
    }
}
