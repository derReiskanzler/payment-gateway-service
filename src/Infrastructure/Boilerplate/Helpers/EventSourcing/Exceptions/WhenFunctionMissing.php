<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions;

use RuntimeException;

final class WhenFunctionMissing extends RuntimeException
{
    public static function forEventWithName(string $eventName, string $aggregateType): self
    {
        return new self(
            sprintf('Aggregate %s is missing a when%s method.', $aggregateType, $eventName)
        );
    }
}
