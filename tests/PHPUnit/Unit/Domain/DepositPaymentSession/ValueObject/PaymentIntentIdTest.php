<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentIntentId;
use PHPUnit\Framework\TestCase;

final class PaymentIntentIdTest extends TestCase
{
    public function testFromString(): void
    {
        $paymentIntentId = PaymentIntentId::fromString('pi_1Dr1jX2eZvKYlo2C6r0iT7PO');

        $this->assertInstanceOf(
            PaymentIntentId::class,
            $paymentIntentId,
            'created payment intent id from string does not match expected class: PaymentIntentId.'
        );
    }

    public function testToString(): void
    {
        $string = 'pi_1Dr1jX2eZvKYlo2C6r0iT7PO';
        $paymentIntentId = PaymentIntentId::fromString($string);

        $this->assertEquals(
            $string,
            $paymentIntentId->toString(),
            'created payment intent id to string does not match expected string.',
        );
    }
}
