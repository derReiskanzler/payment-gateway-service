<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\ErrorCount;
use PHPUnit\Framework\TestCase;

final class ErrorCountTest extends TestCase
{
    public function testFromInt(): void
    {
        $errorCount = ErrorCount::fromInt(1);

        $this->assertInstanceOf(
            ErrorCount::class,
            $errorCount,
            'created error count from string is not instance of expected class: ErrorCount.',
        );
    }

    public function testToInt(): void
    {
        $int = 1;
        $errorCount = ErrorCount::fromInt($int);

        $this->assertEquals(
            $int,
            $errorCount->toInt(),
            'error count to int does not match expected int.',
        );
    }

    public function testIncrease(): void
    {
        $int = 1;
        $errorCount = ErrorCount::fromInt($int);
        $this->assertEquals(
            ++$int,
            $errorCount->increase()->toInt(),
            'increased error count to int does not match expected int.',
        );
    }

    public function testExceedsErrorCount(): void
    {
        $int = 2;
        $errorCount = ErrorCount::fromInt($int);
        $this->assertEquals(
            false,
            $errorCount->exceedsMaxErrorCount(),
            'exceeds error count check does not match expected bool.',
        );
    }

    public function testExceedsErrorCountWithExceededCount(): void
    {
        $int = 7;
        $errorCount = ErrorCount::fromInt($int);
        $this->assertEquals(
            true,
            $errorCount->exceedsMaxErrorCount(),
            'exceeds error count check does not match expected bool.',
        );
    }
}
