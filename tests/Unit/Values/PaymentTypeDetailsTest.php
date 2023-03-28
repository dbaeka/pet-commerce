<?php

namespace Tests\Unit\Values;

use App\Enums\PaymentType;
use App\Values\PaymentType\BankTransferDetails;
use App\Values\PaymentType\CashOnDeliveryDetails;
use App\Values\PaymentType\CreditCardDetails;
use InvalidArgumentException;
use Tests\TestCase;

class PaymentTypeDetailsTest extends TestCase
{
    public function testCreatesValueFromArray(): void
    {
        $data = [
            'name' => 'foo',
            'iban' => 'bar',
            'swift' => 'baz'
        ];

        $payment_type = BankTransferDetails::fromArray(
            $data,
            PaymentType::BANK_TRANSFER
        );

        self::assertInstanceOf(BankTransferDetails::class, $payment_type);
        self::assertSame('foo', $payment_type->name);

        $data = [
            'holder_name' => 'foo',
            'number' => 'bar',
            'cvv' => 'baz',
            'expiry_date' => '2022-01-01'
        ];

        $payment_type = CreditCardDetails::fromArray(
            $data,
            PaymentType::BANK_TRANSFER
        );

        self::assertInstanceOf(CreditCardDetails::class, $payment_type);
        self::assertSame('foo', $payment_type->holder_name);

        $data = [
            'first_name' => 'foo',
            'last_name' => 'bar',
            'address_line1' => 'baz',
            'address_line2' => 'bae',
            'consent' => false,
            'text' => 'Yeehaw'
        ];

        $payment_type = CashOnDeliveryDetails::fromArray(
            $data,
            PaymentType::BANK_TRANSFER
        );

        self::assertInstanceOf(CashOnDeliveryDetails::class, $payment_type);
        self::assertSame('foo', $payment_type->first_name);
    }

    public function testGetType(): void
    {
        $data = [
            'name' => 'foo',
            'iban' => 'bar',
            'swift' => 'baz'
        ];

        $payment_type = BankTransferDetails::fromArray(
            $data,
            PaymentType::BANK_TRANSFER
        );

        self::assertInstanceOf(PaymentType::class, $payment_type->getType());
        self::assertSame(PaymentType::BANK_TRANSFER, $payment_type->getType());
    }


    public function testFailsCreateBankTransferDetailsValueFromWrongArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $data = [
            'names' => 'foo',
            'iban' => 'bar',
            'swift' => 'baz'
        ];

        BankTransferDetails::fromArray(
            $data,
            PaymentType::BANK_TRANSFER
        );
    }

    public function testFailsCreateCreditCardDetailsValueFromWrongArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $data = [
            'holder_name' => 'foo',
            'numbered' => 'bar',
            'cvv' => 'baz',
            'expiry_date' => '2022-01-01'
        ];

        CreditCardDetails::fromArray(
            $data,
            PaymentType::BANK_TRANSFER
        );
    }

    public function testFailsCreateCashOnDeliveryDetailsValueFromWrongArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $data = [
            'first_name' => 'foo',
            'last_name' => 'bar',
            'addresses' => 'baz'
        ];
        CashOnDeliveryDetails::fromArray(
            $data,
            PaymentType::BANK_TRANSFER
        );
    }
}
