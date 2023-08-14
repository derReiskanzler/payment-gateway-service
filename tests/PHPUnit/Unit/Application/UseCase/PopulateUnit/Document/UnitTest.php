<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\PopulateUnit\Document;

use Allmyhomes\Application\UseCase\PopulateUnit\Document\Unit;
use Allmyhomes\Domain\ValueObject\UnitId;
use Allmyhomes\Domain\ValueObject\UnitName;
use PHPUnit\Framework\TestCase;

final class UnitTest extends TestCase
{
    /**
     * @param array<string, mixed> $unitData
     *
     * @dataProvider getUnitData
     */
    public function testId(array $unitData): void
    {
        $unit = Unit::fromArray($unitData);

        $this->assertEquals($unitData['id'], $unit->id()->toInt(), 'unit id does not match expected unit id.');
    }

    /**
     * @param array<string, mixed> $unitData
     *
     * @dataProvider getUnitData
     */
    public function testName(array $unitData): void
    {
        $unit = Unit::fromArray($unitData);

        $this->assertEquals($unitData['name'], $unit->name()?->toString(), 'unit name does not match expected unit name.');
    }

    /**
     * @param array<string, mixed> $unitData
     *
     * @dataProvider getUnitData
     */
    public function testToArray(array $unitData): void
    {
        $unit = new Unit(
            UnitId::fromInt($unitData['id']),
            UnitName::fromString($unitData['name']),
        );

        $this->assertEquals(
            $unitData,
            $unit->toArray(),
            'unit data does not match expected unit data from toArray method',
        );
    }

    /**
     * @param array<string, mixed> $unitData
     *
     * @dataProvider getUnitData
     */
    public function testFromArray(array $unitData): void
    {
        $unit = new Unit(
            UnitId::fromInt($unitData['id']),
            UnitName::fromString($unitData['name']),
        );
        $fromUnit = Unit::fromArray($unitData);

        $this->assertEquals(
            $unit->toArray(),
            $fromUnit->toArray(),
            'unit data does not match expected unit data from fromArray method',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getUnitData(): array
    {
        return [
            'unit_data' => [
                'unit_data' => [
                    'id' => 42,
                    'name' => 'WE 01',
                ],
            ],
        ];
    }
}
