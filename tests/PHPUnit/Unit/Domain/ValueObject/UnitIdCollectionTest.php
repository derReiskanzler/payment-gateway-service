<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\UnitIdCollection;
use Generator;
use PHPUnit\Framework\TestCase;

final class UnitIdCollectionTest extends TestCase
{
    /**
     * @param int[] $unitIds
     * @dataProvider provideUnitIds
     */
    public function testFromArray(array $unitIds): void
    {
        $unitIdCollection = UnitIdCollection::fromArray($unitIds);

        $this->assertInstanceOf(
            UnitIdCollection::class,
            $unitIdCollection,
            'unit id array of unit id collection does not match expected unit id array.'
        );
    }

    /**
     * @param int[] $unitIds
     * @dataProvider provideUnitIds
     */
    public function testToArray(array $unitIds): void
    {
        $unitIdCollection = UnitIdCollection::fromArray($unitIds);

        $this->assertEquals(
            $unitIds,
            $unitIdCollection->toArray(),
            'unit id array of unit id collection does not match expected unit id array.'
        );
    }

    public function provideUnitIds(): Generator
    {
        yield 'UnitIds' => [
            'unit ids' => [
                1, 2, 3,
            ],
        ];
    }
}
