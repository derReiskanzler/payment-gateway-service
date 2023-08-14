<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\ContextStreamName;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\InvalidStreamNameFormat;
use PHPUnit\Framework\TestCase;

class ContextStreamNameTest extends TestCase
{
    public function testStreamNameEnsuresLowerCaseIsGiven(): void
    {
        $streamName = ContextStreamName::fromString('some_context-event-stream');

        static::assertSame('some_context-event-stream', (string) $streamName);

        $this->expectException(InvalidStreamNameFormat::class);
        ContextStreamName::fromString('some_context-StreamWithoutContext-stream');
        ContextStreamName::fromString('some_context-StreamWithoutContext-stream');
    }
}
