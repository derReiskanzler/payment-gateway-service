<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class CheckoutSessionStatusTest extends TestCase
{
    public function testFromString(): void
    {
        $checkoutSessionStatus = CheckoutSessionStatus::fromString('open');

        $this->assertInstanceOf(
            CheckoutSessionStatus::class,
            $checkoutSessionStatus,
            'created checkout session status from string does not match expected class: CheckoutSessionStatus.'
        );
    }

    public function testToString(): void
    {
        $string = CheckoutSessionStatus::OPEN;
        $checkoutSessionStatus = CheckoutSessionStatus::fromString($string);

        $this->assertEquals(
            $string,
            $checkoutSessionStatus->toString(),
            'checkout session status to string does not match expected string.',
        );
    }

    public function testMatches(): void
    {
        $string = CheckoutSessionStatus::COMPLETE;
        $result = CheckoutSessionStatus::fromString($string)->matches(CheckoutSessionStatus::COMPLETE);

        $this->assertEquals(
            true,
            $result,
            'result does not match expected bool.',
        );
    }

    public function testMatchesWithWrongStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $string = CheckoutSessionStatus::COMPLETE;
        CheckoutSessionStatus::fromString($string)->matches('invalid status');
    }

    public function testMatchesWithMismatchingStatus(): void
    {
        $string = CheckoutSessionStatus::COMPLETE;
        $result = CheckoutSessionStatus::fromString($string)->matches(CheckoutSessionStatus::OPEN);

        $this->assertEquals(
            false,
            $result,
            'result does not match expected bool.',
        );
    }

    public function testCouldNotCreatePaymentStatusException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CheckoutSessionStatus::fromString('some_status');
    }
}
