<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventName;
use RuntimeException;
use function sprintf;

final class EventNameMappingMissing extends RuntimeException
{
    public static function forEvent(DomainEvent $event): self
    {
        return new self(sprintf('Missing an event name mapping for event class: %s', $event::class));
    }

    public static function forName(EventName $eventName): self
    {
        return new self(sprintf('Missing an event class mapping for event name: %s', $eventName));
    }
}
