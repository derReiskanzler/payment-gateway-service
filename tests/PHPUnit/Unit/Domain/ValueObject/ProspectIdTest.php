<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\ProspectId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ProspectIdTest extends TestCase
{
    public function testFromString(): void
    {
        $prospectId = ProspectId::fromString('ca50819f-e5a4-40d3-a425-daba3e095407');

        self::assertInstanceOf(
            ProspectId::class,
            $prospectId,
            'created prospect id from string is not instance of expected class: ProspectId.'
        );
    }

    public function testInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ProspectId::fromString('foo');
    }

    public function testToString(): void
    {
        $string = 'ca50819f-e5a4-40d3-a425-daba3e095407';
        $prospectId = ProspectId::fromString($string);
        self::assertEquals(
            $string,
            $prospectId->toString(),
            'created prospect id from string does not match expected string.',
        );
    }
}
