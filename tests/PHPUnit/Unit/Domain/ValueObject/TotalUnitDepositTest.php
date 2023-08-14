<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\TotalUnitDeposit;
use PHPUnit\Framework\TestCase;

final class TotalUnitDepositTest extends TestCase
{
    public function testFromFloat(): void
    {
        $unitDeposit = TotalUnitDeposit::fromFloat(42.00);
        $this->assertInstanceOf(
            TotalUnitDeposit::class,
            $unitDeposit,
            'created total unit deposit from float is not instance of expected class: TotalUnitDeposit.',
        );
    }

    public function testFromFloatWithInt(): void
    {
        $unitDeposit = TotalUnitDeposit::fromFloat(42);
        $this->assertInstanceOf(
            TotalUnitDeposit::class,
            $unitDeposit,
            'created total unit deposit from float with int is not instance of expected class: TotalUnitDeposit.',
        );
    }

    public function testToFloat(): void
    {
        $float = 42.00;
        $unitDeposit = TotalUnitDeposit::fromFloat($float);
        $this->assertEquals(
            $float,
            $unitDeposit->toFloat(),
            'created total unit deposit from float does not match expected float.',
        );
    }

    public function testToFloatWithInt(): void
    {
        $int = 42;
        $unitDeposit = TotalUnitDeposit::fromFloat($int);
        $this->assertEquals(
            $int,
            $unitDeposit->toFloat(),
            'created total unit deposit from float does not match expected int.',
        );
    }

    public function testIsEmpty(): void
    {
        $int = 42;
        $unitDeposit = TotalUnitDeposit::fromFloat($int);
        $this->assertEquals(
            false,
            $unitDeposit->isEmpty(),
            'created total unit deposit from float does not match expected int.',
        );
    }

    public function testIsEmptyWithEmtpyValue(): void
    {
        $int = 0;
        $unitDeposit = TotalUnitDeposit::fromFloat($int);
        $this->assertEquals(
            true,
            $unitDeposit->isEmpty(),
            'created total unit deposit from float does not match expected int.',
        );
    }
}
