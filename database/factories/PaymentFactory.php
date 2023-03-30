<?php

namespace Database\Factories;

use App\Enums\PaymentType;
use App\Values\PaymentType\PaymentTypeDetailsFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->unique()->uuid(),
            'type' => fake()->randomElement(PaymentType::cases()),
            'details' => fn (array $attributes) => PaymentTypeDetailsFactory::make(
                $attributes['type'],
                self::getSampleDetail($attributes['type'])
            ),
        ];
    }

    /**
     * @param PaymentType $type
     * @return array<string,mixed>
     */
    public static function getSampleDetail(PaymentType $type): array
    {
        $details = array();
        switch ($type) {
            case PaymentType::CREDIT_CARD:
                $details = [
                    "holder_name" => fake()->name(),
                    "number" => fake()->creditCardNumber(),
                    "cvv" => fake()->randomNumber(3),
                    "expiry_date" => fake()->date('m/y'),
                ];
                break;
            case PaymentType::CASH_ON_DELIVERY:
                $details = [
                    "first_name" => fake()->firstName(),
                    "last_name" => fake()->lastName(),
                    "address_line1" => fake()->streetAddress(),
                    "address_line2" => fake()->city(),
                    "ref_code" => fake()->sentence(),
                    "consent" => fake()->boolean()
                ];
                break;
            case PaymentType::BANK_TRANSFER:
                $details = [
                    "swift" => fake()->swiftBicNumber(),
                    "name" => fake()->name(),
                    "iban" => fake()->iban(),
                ];
        }
        return $details;
    }
}
