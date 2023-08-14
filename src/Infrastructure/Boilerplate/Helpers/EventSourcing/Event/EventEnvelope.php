<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateType;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateVersion;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;
use Exception;
use InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

final class EventEnvelope
{
    public const UUID = 'uuid';
    public const EVENT = 'event';
    public const EVENT_NAME = 'eventName';
    public const AGGREGATE_ID = 'aggregateId';
    public const AGGREGATE_VERSION = 'aggregateVersion';
    public const AGGREGATE_TYPE = 'aggregateType';
    public const METADATA = 'metadata';
    public const CREATED_AT = 'createdAt';

    /**
     * @param array<string, mixed> $metadata
     *
     * @throws UnsatisfiedDependencyException if `Moontoast\Math\BigNumber` is not present
     * @throws InvalidArgumentException       if the uuid-generator is not configured correctly
     * @throws Exception                      if it was not possible to gather sufficient entropy for the uuid-generator
     */
    public static function wrap(
        DomainEvent $domainEvent,
        EventName $eventName,
        AggregateId $aggregateId,
        AggregateVersion $aggregateVersion,
        AggregateType $aggregateType,
        array $metadata = []
    ): self {
        return new self(
            EventId::generate(),
            $domainEvent,
            $eventName,
            $aggregateId,
            $aggregateVersion,
            $aggregateType,
            $metadata,
            CreatedAt::now()
        );
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public static function reconstitute(
        EventId $eventId,
        DomainEvent $domainEvent,
        EventName $eventName,
        AggregateId $aggregateId,
        AggregateVersion $aggregateVersion,
        AggregateType $aggregateType,
        array $metadata,
        CreatedAt $createdAt
    ): self {
        return new self(
            $eventId,
            $domainEvent,
            $eventName,
            $aggregateId,
            $aggregateVersion,
            $aggregateType,
            $metadata,
            $createdAt
        );
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function __construct(
        private EventId $eventId,
        private DomainEvent $event,
        private EventName $eventName,
        private AggregateId $aggregateId,
        private AggregateVersion $aggregateVersion,
        private AggregateType $aggregateType,
        private array $metadata,
        private CreatedAt $createdAt
    ) {
    }

    public function eventId(): EventId
    {
        return $this->eventId;
    }

    public function event(): DomainEvent
    {
        return $this->event;
    }

    public function eventName(): EventName
    {
        return $this->eventName;
    }

    public function aggregateId(): AggregateId
    {
        return $this->aggregateId;
    }

    public function aggregateVersion(): AggregateVersion
    {
        return $this->aggregateVersion;
    }

    public function aggregateType(): AggregateType
    {
        return $this->aggregateType;
    }

    public function createdAt(): CreatedAt
    {
        return $this->createdAt;
    }

    public function withAddedMetadata(string $key, mixed $value): self
    {
        $copy = clone $this;
        $copy->metadata[$key] = $value;

        return $copy;
    }

    /**
     * Return value of $key.
     *
     * If $key is null, metadata array is returned
     * If $key does not exist, $default is returned instead
     */
    public function metadata(?string $key = null, mixed $default = null): mixed
    {
        if (null === $key) {
            return $this->metadata;
        }

        if (!\array_key_exists($key, $this->metadata)) {
            return $default;
        }

        return $this->metadata[$key];
    }
}
