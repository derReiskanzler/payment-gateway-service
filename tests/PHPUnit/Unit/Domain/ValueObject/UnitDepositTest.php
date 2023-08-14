<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\UnitDeposit;
use PHPUnit\Framework\TestCase;

final class UnitDepositTest extends TestCase
{
    public function testFromFloat(): void
    {
        $unitDeposit = UnitDeposit::fromFloat(42.00);

        $this->assertInstanceOf(
            UnitDeposit::class,
            $unitDeposit,
            'created unit deposit from float does not match expected class: UnitDeposit.',
        );
    }

    public function testFromFloatWithInt(): void
    {
        $unitDeposit = UnitDeposit::fromFloat(42);

        $this->assertInstanceOf(
            UnitDeposit::class,
            $unitDeposit,
            'created unit deposit from float with int does not match expected class: UnitDeposit.',
        );
    }

    public function testToFloat(): void
    {
        $float = 42.00;
        $unitDeposit = UnitDeposit::fromFloat($float);

        $this->assertEquals(
            $float,
            $unitDeposit->toFloat(),
            'unit deposit to float does not match expected float.',
        );
    }

    public function testToFloatWithInt(): void
    {
        $int = 42;
        $unitDeposit = UnitDeposit::fromFloat($int);

        $this->assertEquals(
            $int,
            $unitDeposit->toFloat(),
            'unit deposit to float does not match expected int.',
        );
    }

    public function testToCents(): void
    {
        $float = 42.31;
        $unitDeposit = UnitDeposit::fromFloat($float);

        $this->assertEquals(
            $float * 100,
            $unitDeposit->toCents(),
            'unit deposit to cents does not match expected float.',
        );
    }

    public function testToCentsWithInt(): void
    {
        $int = 42;
        $unitDeposit = UnitDeposit::fromFloat($int);

        $this->assertEquals(
            $int * 100,
            $unitDeposit->toCents(),
            'unit deposit to cents does not match expected int.',
        );
    }
}
