<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\Unit;
use Generator;
use PHPUnit\Framework\TestCase;

final class UnitTest extends TestCase
{
    /**
     * @param array<string, mixed> $unitArray
     * @dataProvider provideUnitData
     */
    public function testFromArray(array $unitArray): void
    {
        $unit = Unit::fromArray($unitArray);

        $this->assertInstanceOf(
            Unit::class,
            $unit,
        );
    }

    /**
     * @param array<string, mixed> $unitArray
     * @dataProvider provideUnitData
     */
    public function testToArray(array $unitArray): void
    {
        $unit = Unit::fromArray($unitArray);

        $this->assertEquals(
            $unitArray,
            $unit->toArray()
        );
    }

    /**
     * @param array<string, mixed> $unitArray
     * @dataProvider provideUnitData
     */
    public function testId(array $unitArray): void
    {
        $unit = Unit::fromArray($unitArray);

        $this->assertEquals(
            1,
            $unit->id()->toInt()
        );
    }

    /**
     * @param array<string, mixed> $unitArray
     * @dataProvider provideUnitData
     */
    public function testDeposit(array $unitArray): void
    {
        $unit = Unit::fromArray($unitArray);

        $this->assertEquals(
            3000.00,
            $unit->deposit()?->toFloat()
        );
    }

    /**
     * @param array<string, mixed> $unitArray
     * @dataProvider provideUnitData
     */
    public function testName(array $unitArray): void
    {
        $unit = Unit::fromArray($unitArray);

        $this->assertEquals(
            'WE 1',
            $unit->name()?->toString()
        );
    }

    public function provideUnitData(): Generator
    {
        yield 'Unit data' => [
            'unit data array' => [
                'id' => 1,
                'deposit' => 3000.00,
                'name' => 'WE 1',
            ],
        ];
    }
}
