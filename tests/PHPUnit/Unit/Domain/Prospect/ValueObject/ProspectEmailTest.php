<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\Prospect\ValueObject;

use Allmyhomes\Domain\Prospect\ValueObject\ProspectEmail;
use PHPUnit\Framework\TestCase;

final class ProspectEmailTest extends TestCase
{
    public function testFromString(): void
    {
        $email = ProspectEmail::fromString('max.mustermann@gmail.com');

        $this->assertInstanceOf(
            ProspectEmail::class,
            $email,
            'created prospect email from string is not instance of expected class: ProspectEmail.'
        );
    }

    public function testToString(): void
    {
        $string = 'max.mustermann@gmail.com';
        $email = ProspectEmail::fromString($string);

        $this->assertEquals(
            $string,
            $email->toString(),
            'created prospect email from string does not match expected string.',
        );
    }
}
