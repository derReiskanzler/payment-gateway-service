<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use DateTimeImmutable;
use DateTimeZone;
use Generator;
use InvalidArgumentException;
use Iterator;
use PHPUnit\Framework\TestCase;

final class ExpiresAtTest extends TestCase
{
    /**
     * @return Generator<array<string>>
     */
    public function provideUnprocessableStrings(): Iterator
    {
        yield 'foo' => ['foo'];
        yield 'wrong format' => ['2020-02-02 20'];
    }

    /**
     * @dataProvider provideUnprocessableStrings
     */
    public function testThrowsInvalidArgumentExceptionIfStringIsInvalid(string $invalidString): void
    {
        $this->expectException(InvalidArgumentException::class);
        ExpiresAt::fromString($invalidString);
    }

    /**
     * @return Generator<array<string>>
     */
    public function provideProcessableStrings(): Iterator
    {
        yield 'Y-m-d\TH:i:s.Z' => ['2020-07-16T16:00:00Z'];
        yield 'Iso8601 without offset' => ['2020-07-16T16:00:00+00:00'];
        yield 'Iso8601 with offset' => ['2020-07-16T17:00:00+01:00'];
        yield 'Y-m-d\TH:i:s.u' => ['2020-07-16T16:00:00.000000'];
        yield 'Y-m-d\TH:i:s' => ['2020-07-16T16:00:00'];
        yield 'Y-m-d H:i:s.u' => ['2020-07-16 16:00:00.000000'];
    }

    /**
     * @throws \Exception
     */
    public function testFromDateTime(): void
    {
        $dateTime = ExpiresAt::fromDateTime(new DateTimeImmutable('2020-07-16T16:00:00.000000'));

        self::assertInstanceOf(
            ExpiresAt::class,
            $dateTime,
            'created date time from date time immutable does not match expected class: OccurredAt.',
        );
    }

    public function testFromSeconds(): void
    {
        $dateTime = ExpiresAt::fromSeconds(1653004800);

        self::assertInstanceOf(
            ExpiresAt::class,
            $dateTime,
            'created date time from seconds does not match expected class: OccurredAt.',
        );
    }

    public function testFromString(): void
    {
        $dateTime = ExpiresAt::fromString('2020-07-16T16:00:00.000000');

        self::assertInstanceOf(
            ExpiresAt::class,
            $dateTime,
            'created date time from string does not match expected class: OccurredAt.',
        );
    }

    /**
     * @dataProvider provideProcessableStrings
     *
     * @throws \Exception
     */
    public function testDateTime(string $processableString): void
    {
        $dateTimeImmutable = new DateTimeImmutable($processableString);
        $date = ExpiresAt::fromDateTime($dateTimeImmutable);

        self::assertEquals(
            $dateTimeImmutable,
            $date->dateTime(),
            'date time immutable from date time does not match expected date time immutable.',
        );
    }

    public function testToSeconds(): void
    {
        $seconds = 1653004800;
        $dateTime = ExpiresAt::fromSeconds($seconds);

        self::assertEquals(
            $seconds,
            $dateTime->toSeconds(),
            'date time to seconds does not match expected seconds.',
        );
    }

    public function testToString(): void
    {
        $dateString = '2022-05-20T00:00:00.000000';
        $dateTime = ExpiresAt::fromString($dateString);

        self::assertEquals(
            $dateString,
            $dateTime->toString(),
            'date time to string does not match expected string.',
        );
    }

    /**
     * @dataProvider provideFormats
     */
    public function testFormat(string $format, string $dateString, string $expected): void
    {
        $dateTime = ExpiresAt::fromString($dateString);

        self::assertEquals(
            $expected,
            $dateTime->format($format),
            'date time to string does not match expected string.',
        );
    }

    public function provideFormats(): Generator
    {
        yield 'English date format' => [
            'format' => Context::EN_TIME_FORMAT,
            'date string' => '2022-05-20T00:00:00.000000',
            'expected' => '20.05.22',
        ];

        yield 'German date format' => [
            'format' => Context::DE_TIME_FORMAT,
            'date string' => '2022-05-20T00:00:00.000000',
            'expected' => '20.05.22',
        ];
    }

    /**
     * @throws \Exception
     */
    public function testFromDateTimeWithNonUtcDate(): void
    {
        $dateString = '2016-06-16T16:00:00+00:00';
        $timezone = new DateTimeZone('America/New_York');
        $dateTimeImmutable = new DateTimeImmutable($dateString, $timezone);

        $date = ExpiresAt::fromDateTime($dateTimeImmutable);

        self::assertEquals(
            $dateTimeImmutable->format(Context::DEFAULT_TIME_FORMAT),
            $date->toString(),
            'date time immutable from date time does not match expected date time immutable',
        );
    }

    /**
     * @return Generator<mixed>
     */
    public function provideComparableOccurredAt(): Iterator
    {
        yield 'before' => [
            ExpiresAt::fromString('2016-06-16T16:00:00+00:00'),
            ExpiresAt::fromString('2015-05-15T15:00:00+00:00'),
            false,
        ];
        yield 'same' => [
            ExpiresAt::fromString('2016-06-16T16:00:00+00:00'),
            ExpiresAt::fromString('2016-06-16T16:00:00+00:00'),
            true,
        ];
        yield 'after' => [
            ExpiresAt::fromString('2016-06-16T16:00:00+00:00'),
            ExpiresAt::fromString('2017-07-17T17:00:00+00:00'),
            true,
        ];
    }
}
