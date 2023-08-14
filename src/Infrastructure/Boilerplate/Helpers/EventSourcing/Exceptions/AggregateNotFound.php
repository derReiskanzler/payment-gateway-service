<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateType;
use RuntimeException;

final class AggregateNotFound extends RuntimeException
{
    public static function withId(AggregateId $aggregateId, AggregateType $aggregateType): self
    {
        return new self(sprintf('Aggregate %s with id %s not found.', $aggregateType, $aggregateId));
    }
}
