<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\Language;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class LanguageTest extends TestCase
{
    public function testFromString(): void
    {
        $language = Language::fromString('en');
        $this->assertInstanceOf(
            Language::class,
            $language,
            'created language from string is not instance of expected class: Language.',
        );
    }

    public function testFromInvalidString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Language::fromString('invalid');
    }

    public function testToString(): void
    {
        $string = 'en';
        $language = Language::fromString($string);
        $this->assertEquals(
            $string,
            $language->toString(),
            'created language from string does not match expected string.',
        );
    }
}
