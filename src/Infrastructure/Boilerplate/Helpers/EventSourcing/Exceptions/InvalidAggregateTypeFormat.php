<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions;

use InvalidArgumentException;

final class InvalidAggregateTypeFormat extends InvalidArgumentException
{
    public static function notDotSeparated(string $aggregateType): self
    {
        return new self(
            sprintf('Invalid aggregate type given. Context is not separated by a dot or missing. Got %s', $aggregateType)
        );
    }
}
