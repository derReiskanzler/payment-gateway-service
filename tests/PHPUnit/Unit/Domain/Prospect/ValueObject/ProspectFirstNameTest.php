<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\Prospect\ValueObject;

use Allmyhomes\Domain\Prospect\ValueObject\ProspectFirstName;
use PHPUnit\Framework\TestCase;

final class ProspectFirstNameTest extends TestCase
{
    public function testFromString(): void
    {
        $email = ProspectFirstName::fromString('Max');

        $this->assertInstanceOf(
            ProspectFirstName::class,
            $email,
            'created prospect first name from string is not instance of expected class: ProspectFirstName.'
        );
    }

    public function testToString(): void
    {
        $string = 'Max';
        $email = ProspectFirstName::fromString($string);

        $this->assertEquals(
            $string,
            $email->toString(),
            'created prospect first name from string does not match expected string.',
        );
    }
}
