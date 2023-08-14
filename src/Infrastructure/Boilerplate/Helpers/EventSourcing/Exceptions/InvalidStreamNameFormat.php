<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions;

use InvalidArgumentException;

final class InvalidStreamNameFormat extends InvalidArgumentException
{
    public static function notLowerCase(string $streamName): self
    {
        return new self(
            sprintf('Invalid stream name given. Context or Stream table contains upper letters. Got %s', $streamName)
        );
    }

    public static function sharedEventStreamMissingContext(string $streamName): self
    {
        return new self(
            sprintf('Events Stream, that will be migrated on the EventStore, should have this format service_name-stream_name-stream. Got %s', $streamName)
        );
    }
}
