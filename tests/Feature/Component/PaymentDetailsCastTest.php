<?php

namespace Component;

use App\DataObjects\PaymentType\BankTransferDetails;
use App\DataObjects\PaymentType\CashOnDeliveryDetails;
use App\DataObjects\PaymentType\CreditCardDetails;
use App\Enums\PaymentType;
use App\Models\Payment;
use Database\Factories\PaymentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        /** @var CreditCardDetails $details */
        $details = $payment->details;
        self::assertInstanceOf(CreditCardDetails::class, $details);
        self::assertNotEmpty($details->holder_name);
        self::assertNotEmpty($details->number);
        self::assertNotEmpty($details->cvv);
        self::assertNotEmpty($details->expiry_date);

        /** @var Payment $payment */
        $payment = PaymentFactory::new()->create([
            'type' => PaymentType::BANK_TRANSFER,
        ]);
        /** @var BankTransferDetails $details */
        $details = $payment->details;
        self::assertInstanceOf(BankTransferDetails::class, $details);
        self::assertNotEmpty($details->name);
        self::assertNotEmpty($details->iban);
        self::assertNotEmpty($details->swift);

        /** @var Payment $payment */
        $payment = PaymentFactory::new()->create([
            'type' => PaymentType::CASH_ON_DELIVERY,
        ]);

        /** @var CashOnDeliveryDetails $details */
        $details = $payment->details;
        self::assertInstanceOf(CashOnDeliveryDetails::class, $details);
        self::assertNotEmpty($details->first_name);
        self::assertNotEmpty($details->last_name);
        self::assertNotEmpty($details->address_line1);

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
}
