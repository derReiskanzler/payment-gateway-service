<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitAmount;
use PHPUnit\Framework\TestCase;

final class UnitAmountTest extends TestCase
{
    public function testFromFloat(): void
    {
        $unitAmount = UnitAmount::fromFloat(42.00);

        $this->assertInstanceOf(
            UnitAmount::class,
            $unitAmount,
            'created unit amount from float does not match expected class: UnitAmount.',
        );
    }

    public function testFromFloatWithInt(): void
    {
        $unitAmount = UnitAmount::fromFloat(42);

        $this->assertInstanceOf(
            UnitAmount::class,
            $unitAmount,
            'created unit amount from float with int does not match expected class: UnitAmount.',
        );
    }

    public function testToFloat(): void
    {
        $float = 42.00;
        $unitAmount = UnitAmount::fromFloat($float);

        $this->assertEquals(
            $float,
            $unitAmount->toFloat(),
            'unit amount to float does not match expected float.',
        );
    }

    public function testToFloatWithInt(): void
    {
        $int = 42;
        $unitAmount = UnitAmount::fromFloat($int);

        $this->assertEquals(
            $int,
            $unitAmount->toFloat(),
            'unit amount to float does not match expected int.',
        );
    }

    public function testToCents(): void
    {
        $float = 42.31;
        $unitAmount = UnitAmount::fromFloat($float);

        $this->assertEquals(
            $float * 100,
            $unitAmount->toCents(),
            'unit amount to cents does not match expected float.',
        );
    }

    public function testToCentsWithInt(): void
    {
        $int = 42;
        $unitAmount = UnitAmount::fromFloat($int);

        $this->assertEquals(
            $int * 100,
            $unitAmount->toCents(),
            'unit amount to cents does not match expected int.',
        );
    }
}
