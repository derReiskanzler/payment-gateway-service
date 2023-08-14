<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\Prospect\ValueObject;

use Allmyhomes\Domain\Prospect\ValueObject\ProspectSalutation;
use Allmyhomes\Domain\ValueObject\Language;
use Generator;
use PHPUnit\Framework\TestCase;

final class ProspectSalutationTest extends TestCase
{
    public function testFromInt(): void
    {
        $salutation = ProspectSalutation::fromInt(0);

        $this->assertInstanceOf(
            ProspectSalutation::class,
            $salutation,
            'created prospect salutation from string is not instance of expected class: ProspectSalutation.'
        );
    }

    public function testToInt(): void
    {
        $int = 0;
        $salutation = ProspectSalutation::fromInt($int);

        $this->assertEquals(
            $int,
            $salutation->toInt(),
            'created prospect salutation from int does not match expected int.',
        );
    }

    /**
     * @dataProvider provideIntegerSalutationsAndLanguages
     */
    public function testToStringByLanguage(int $salutation, string $language, string $expected): void
    {
        $salutation = ProspectSalutation::fromInt($salutation);

        $this->assertEquals(
            $expected,
            $salutation->toStringByLanguage(Language::fromString($language)),
            'created prospect salutation to string by language does not match expected string.',
        );
    }

    public function provideIntegerSalutationsAndLanguages(): Generator
    {
        yield 'male salutation and german language' => [
            'salutation' => 0,
            'language' => 'de',
            'expected' => ProspectSalutation::MR_DE,
        ];

        yield 'female salutation and german language' => [
            'salutation' => 1,
            'language' => 'de',
            'expected' => ProspectSalutation::MRS_DE,
        ];

        yield 'other salutation and german language' => [
            'salutation' => 2,
            'language' => 'de',
            'expected' => '',
        ];

        yield 'male salutation and english language' => [
            'salutation' => 0,
            'language' => 'en',
            'expected' => ProspectSalutation::MR_EN,
        ];

        yield 'female salutation and english language' => [
            'salutation' => 1,
            'language' => 'en',
            'expected' => ProspectSalutation::MRS_EN,
        ];

        yield 'other salutation and english language' => [
            'salutation' => 2,
            'language' => 'en',
            'expected' => '',
        ];
    }
}
