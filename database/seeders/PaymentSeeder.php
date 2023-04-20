<?php

namespace Database\Seeders;

use App\DataObjects\PaymentType\PaymentTypeDetailsFactory;
use App\Enums\PaymentType;
use Database\Factories\PaymentFactory;

class PaymentSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payments = [];
        for ($i = 0; $i < 150; $i++) {
            $type = fake()->randomElement(PaymentType::cases());
            $payments[] = [
                'uuid' => fake()->unique()->uuid(),
                'type' => $type->value,
                'details' => json_encode(PaymentTypeDetailsFactory::make(
                    $type,
                    PaymentFactory::getSampleDetail($type)
                )),
            ];
        }
        $this->syncToDb($payments);
    }
}
