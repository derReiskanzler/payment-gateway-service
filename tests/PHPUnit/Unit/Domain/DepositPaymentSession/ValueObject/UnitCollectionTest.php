<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Exception\UnitsNotFoundException;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\LineItemCollection;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\Unit;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\Reservation\ValueObject\UnitCollection as ReservationUnitCollection;
use Allmyhomes\Domain\Unit\ValueObject\UnitCollection as UnitReadModelCollection;
use Generator;
use PHPUnit\Framework\TestCase;

final class UnitCollectionTest extends TestCase
{
    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testFromReadModelUnitCollections(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromReadModelUnitCollections(
            UnitReadModelCollection::fromArray([
                $unit1,
                $unit2,
            ]),
            ReservationUnitCollection::fromArray([
                $unit1,
                $unit2,
            ]),
        );

        $this->assertInstanceOf(
            UnitCollection::class,
            $unitCollection,
            'created unit collection from reservation unit collection and unit readmodel collection does not match expected class: UnitCollection.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testFromReadModelUnitCollectionsFailed(array $unit1, array $unit2): void
    {
        $this->expectException(UnitsNotFoundException::class);

        UnitCollection::fromReadModelUnitCollections(
            UnitReadModelCollection::fromArray([
                $unit1,
            ]),
            ReservationUnitCollection::fromArray([
                $unit2,
            ]),
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testFromArray(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $this->assertInstanceOf(
            UnitCollection::class,
            $unitCollection,
            'created unit collection from array does not match expected class: UnitCollection.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testFromUnits(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromUnits(
            Unit::fromArray($unit1),
            Unit::fromArray($unit2)
        );

        $this->assertInstanceOf(
            UnitCollection::class,
            $unitCollection,
            'created unit collection from units does not match expected class: UnitCollection.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testUnits(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $this->assertEquals(
            [
                0 => Unit::fromArray($unit1),
                1 => Unit::fromArray($unit2),
            ],
            $unitCollection->units(),
            'unit collection does not match expected unit collection.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testToArray(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $this->assertEquals(
            [
                0 => $unit1,
                1 => $unit2,
            ],
            $unitCollection->toArray(),
            'unit collection to array does not match expected unit collection.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @param array<string, mixed> $unit3
     * @dataProvider provideUnits
     */
    public function testAdd(array $unit1, array $unit2, array $unit3): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $unitCollection = $unitCollection->add(Unit::fromArray($unit3));

        $this->assertEquals(
            [
                0 => $unit1,
                1 => $unit2,
                2 => $unit3,
            ],
            $unitCollection->toArray(),
            'unit collection after adding a unit does not match expected unit collection.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testCount(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $this->assertEquals(
            2,
            $unitCollection->count(),
            'count of unit collection does not match expected number.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @param array<string, mixed> $unit3
     * @dataProvider provideUnits
     */
    public function testCountAfterAdd(array $unit1, array $unit2, array $unit3): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $unitCollection = $unitCollection->add(Unit::fromArray($unit3));

        $this->assertEquals(
            3,
            $unitCollection->count(),
            'count of unit collection after adding a unit does not match expected number.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testTotalUnitDeposit(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $this->assertEquals(
            900.00,
            $unitCollection->totalUnitDeposit()->toFloat(),
            'total unit deposit of unit collection to float does not match expected total unit deposit.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testIds(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $this->assertEquals(
            [
                0 => $unit1['id'],
                1 => $unit2['id'],
            ],
            $unitCollection->ids(),
            'unit id array of unit collection does not match expected unit id array.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testIdCollection(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $unitsIds = [
            0 => $unit1['id'],
            1 => $unit2['id'],
        ];

        $this->assertEquals(
            $unitsIds,
            $unitCollection->idCollection()->toArray(),
            'unit id array of unit id collection from unit collection does not match expected unit id array of unit id collection.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testFindById(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $this->assertEquals(
            $unit1['id'],
            $unitCollection->findById($unit1['id'])?->id()->toInt(),
            'unit id of unit from unit collection does not match expected id.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testFindByIdFailed(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit2,
        ]);

        $this->assertEquals(
            null,
            $unitCollection->findById($unit1['id']),
            'unit id of unit from unit collection does not match expected id.'
        );
    }

    /**
     * @param array<string, mixed> $unit1
     * @param array<string, mixed> $unit2
     * @dataProvider provideUnits
     */
    public function testAdapt(array $unit1, array $unit2): void
    {
        $unitCollection = UnitCollection::fromArray([
            $unit1,
            $unit2,
        ]);

        $lineItemCollection = LineItemCollection::fromArray([
            0 => [
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $unit1['deposit'],
                    'product_data' => [
                        'name' => $unit1['name'],
                        'images' => [],
                    ],
                ],
            ],
            1 => [
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $unit2['deposit'],
                    'product_data' => [
                        'name' => $unit2['name'],
                        'images' => [],
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(
            LineItemCollection::class,
            $unitCollection->adapt(),
            'adapted unit collection does match expected class name: LineItemCollection.',
        );
        $this->assertEquals(
            $lineItemCollection->toArray(),
            $unitCollection->adapt()->toArray(),
            'adapted unit collection does match expected array.',
        );
    }

    public function provideUnits(): Generator
    {
        yield 'Units' => [
            'unit1' => [
                'id' => 1,
                'deposit' => 300.0,
                'name' => 'WE 1',
            ],
            'unit2' => [
                'id' => 2,
                'deposit' => 600.0,
                'name' => 'WE 2',
            ],
            'unit3' => [
                'id' => 3,
                'deposit' => 900.0,
                'name' => 'WE 3',
            ],
        ];
    }
}
