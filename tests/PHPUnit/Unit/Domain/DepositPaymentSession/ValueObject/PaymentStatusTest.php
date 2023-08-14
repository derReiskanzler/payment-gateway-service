<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class PaymentStatusTest extends TestCase
{
    public function testFromString(): void
    {
        $paymentStatus = PaymentStatus::fromString('unpaid');

        $this->assertInstanceOf(
            PaymentStatus::class,
            $paymentStatus,
            'created payment status from string does not match expected class: PaymentStatus.'
        );
    }

    public function testToString(): void
    {
        $string = 'unpaid';
        $paymentStatus = PaymentStatus::fromString($string);

        $this->assertEquals(
            $string,
            $paymentStatus->toString(),
            'payment status to string does not match expected string.',
        );
    }

    public function testMatches(): void
    {
        $string = PaymentStatus::PAID;
        $result = PaymentStatus::fromString($string)->matches(PaymentStatus::PAID);

        $this->assertEquals(
            true,
            $result,
            'result does not match expected bool.',
        );
    }

    public function testMatchesWithWrongStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $string = PaymentStatus::PAID;
        PaymentStatus::fromString($string)->matches('invalid status');
    }

    public function testMatchesWithMismatchingStatus(): void
    {
        $string = PaymentStatus::PAID;
        $result = PaymentStatus::fromString($string)->matches(PaymentStatus::UNPAID);

        $this->assertEquals(
            false,
            $result,
            'result does not match expected bool.',
        );
    }

    public function testCouldNotCreatePaymentStatusException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PaymentStatus::fromString('some_status');
    }
}
