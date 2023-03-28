<?php

namespace Tests\Feature\Component\Cast;

use App\Enums\PaymentType;
use App\Models\Payment;
use App\Values\PaymentType\BankTransferDetails;
use App\Values\PaymentType\CashOnDeliveryDetails;
use App\Values\PaymentType\CreditCardDetails;
use Database\Factories\PaymentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class PaymentDetailsCastTest extends TestCase
{
    use RefreshDatabase;

    public function testCastsPaymentDetailsToPaymentDetailsValue(): void
    {
        /** @var Payment $payment */
        $payment = PaymentFactory::new()->create([
            'type' => PaymentType::CREDIT_CARD,
        ]);
        self::assertInstanceOf(CreditCardDetails::class, $payment->details);
        self::assertNotEmpty($payment->details->holder_name);
        self::assertNotEmpty($payment->details->number);
        self::assertNotEmpty($payment->details->cvv);
        self::assertNotEmpty($payment->details->expiry_date);

        /** @var Payment $payment */
        $payment = PaymentFactory::new()->create([
            'type' => PaymentType::BANK_TRANSFER,
        ]);
        self::assertInstanceOf(BankTransferDetails::class, $payment->details);
        self::assertNotEmpty($payment->details->name);
        self::assertNotEmpty($payment->details->iban);
        self::assertNotEmpty($payment->details->swift);

        /** @var Payment $payment */
        $payment = PaymentFactory::new()->create([
            'type' => PaymentType::CASH_ON_DELIVERY,
        ]);
        self::assertInstanceOf(CashOnDeliveryDetails::class, $payment->details);
        self::assertNotEmpty($payment->details->first_name);
        self::assertNotEmpty($payment->details->last_name);
        self::assertNotEmpty($payment->details->address_line1);

        $payment_details = new CashOnDeliveryDetails(
            'foo',
            'bar',
            'baz',
            'bae',
            'here',
            true
        );
        $payment->details = $payment_details;
        $payment->save();

        /** @var CashOnDeliveryDetails $new_details */
        $new_details = $payment->refresh()->details;
        self::assertSame('foo', $new_details->first_name);
        self::assertSame('bar', $new_details->last_name);
        self::assertSame('baz', $new_details->address_line1);
    }

    public function testFailsCastDetailsWhenWrongValueType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PaymentFactory::new()->create([
            'type' => PaymentType::CASH_ON_DELIVERY,
            'details' => [
                "first_name" => fake()->firstName(),
                "last_name" => fake()->lastName(),
                'address_line1' => fake()->streetAddress
            ]
        ]);
    }
}
