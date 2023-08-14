<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\Prospect\ValueObject;

use Allmyhomes\Domain\Prospect\ValueObject\ProspectLastName;
use PHPUnit\Framework\TestCase;

final class ProspectLastNameTest extends TestCase
{
    public function testFromString(): void
    {
        $lastName = ProspectLastName::fromString('Mustermann');

        $this->assertInstanceOf(
            ProspectLastName::class,
            $lastName,
            'created prospect last name from string is not instance of expected class: ProspectLastName.'
        );
    }

    public function testToString(): void
    {
        $string = 'Mustermann';
        $lastName = ProspectLastName::fromString($string);

        $this->assertEquals(
            $string,
            $lastName->toString(),
            'created prospect last name from string does not match expected string.',
        );
    }
}
