<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions;

use InvalidArgumentException;

final class InvalidEventNameFormat extends InvalidArgumentException
{
    public static function notDotSeparated(string $eventName): self
    {
        return new self(
            sprintf('Invalid event name given. Context is not separated by a dot or missing. Got %s', $eventName)
        );
    }
}
