<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\UnitId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UnitIdTest extends TestCase
{
    public function testFromToInt(): void
    {
        $unitId = UnitId::fromInt(42);
        self::assertEquals(
            42,
            $unitId->toInt(),
            'unit id does not match expected unit id.',
        );
    }

    public function testInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        UnitId::fromInt(-1);
    }

    public function testToString(): void
    {
        $unitId = UnitId::fromInt(42);

        $this->assertEquals(
            '42',
            (string) $unitId,
            'casted unit id to string does not match expected unit id in string format.',
        );
    }
}
