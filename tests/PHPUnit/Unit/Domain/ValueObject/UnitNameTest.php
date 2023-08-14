<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\UnitName;
use PHPUnit\Framework\TestCase;

final class UnitNameTest extends TestCase
{
    public function testFromString(): void
    {
        $this->assertInstanceOf(
            UnitName::class,
            UnitName::fromString('WE 01'),
            'created unit name does not match expected class: UnitName.',
        );
    }

    public function testToString(): void
    {
        $unitNameString = 'WE 01';
        $unitName = UnitName::fromString($unitNameString);

        $this->assertEquals(
            $unitNameString,
            $unitName->toString(),
            'unit name to string does not match expected string.',
        );
    }
}
