<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

class OrderSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = rand(60, 100);
        $orders = [];
        $users = User::where('is_admin', false)->get('uuid');
        $order_statuses = OrderStatus::all(['uuid', 'title']);
        $payments = Payment::has('order', '=', 0)->get('uuid');
        $products = Product::limit($count)->get(['uuid', 'price', 'title']);
        $products = $products->map(function ($item) {
            return [
                'uuid' => $item->uuid,
                'product' => $item->title,
                'price' => $item->price,
                'quantity' => rand(5, 100)
            ];
        });
        for ($i = 0; $i < $count; $i++) {
            /** @var OrderStatus $order_status */
            $order_status = $order_statuses->random();
            /** @var Payment $payment */
            $payment = $payments->shift();
            $orders[] = [
                'user_uuid' => $users->random()->uuid,
                'order_status_uuid' => $order_status->uuid,
                'payment_uuid' => $payment->uuid,
                'uuid' => fake()->unique()->uuid(),
                'products' => json_encode($products->random(rand(1, $count))),
                'address' => json_encode(['shipping' => fake()->streetAddress, 'billing' => fake()->address]),
                'delivery_fee' => fake()->randomFloat(2, 0, 4),
                'amount' => fake()->randomFloat(2, 0, 4),
                'shipped_at' => $order_status->title === 'shipped' ? fake()->date() : null
            ];
        }
        $this->syncToDb($orders);
    }
}
