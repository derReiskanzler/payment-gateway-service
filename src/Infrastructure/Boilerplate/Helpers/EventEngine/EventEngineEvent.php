<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventEnvelope;
use BadMethodCallException;
use DateTimeImmutable;
use EventEngine\Messaging\Exception\RuntimeException;
use EventEngine\Messaging\GenericEvent;
use EventEngine\Messaging\Message;
use EventEngine\Schema\PayloadSchema;
use EventEngine\Schema\Schema;
use EventEngine\Schema\TypeSchemaMap;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class EventEngineEvent implements Message
{
    public static function fromEventEnvelope(EventEnvelope $eventEnvelope): self
    {
        $metadata = $eventEnvelope->metadata();
        $metadata[GenericEvent::META_AGGREGATE_ID] = $eventEnvelope->aggregateId()->toString();
        $metadata[GenericEvent::META_AGGREGATE_VERSION] = $eventEnvelope->aggregateVersion()->toInt();
        $metadata[GenericEvent::META_AGGREGATE_TYPE] = $eventEnvelope->aggregateType()->toString();

        return new self(
            $eventEnvelope->eventName()->toString(),
            $eventEnvelope->eventId()->toString(),
            $eventEnvelope->event()->toArray(),
            $metadata,
            $eventEnvelope->createdAt()->dateTime()
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $metadata
     */
    private function __construct(
        private string $eventName,
        private string $eventId,
        private array $payload,
        private array $metadata,
        private DateTimeImmutable $createdAt
    ) {
    }

    /**
     * Get $key from message payload.
     *
     * @throws RuntimeException if key does not exist in payload
     */
    public function get(string $key): mixed
    {
        if (!\array_key_exists($key, $this->payload)) {
            throw new RuntimeException('Payload key "'.$key.'" does not exist in event '.$this->eventName);
        }

        return $this->payload[$key];
    }

    /**
     * Get $key from message payload or default in case key does not exist.
     */
    public function getOrDefault(string $key, mixed $default): mixed
    {
        if (!\array_key_exists($key, $this->payload)) {
            return $default;
        }

        return $this->payload[$key];
    }

    /**
     * Get $key from message metadata.
     *
     * @throws RuntimeException if key does not exist in metadata
     */
    public function getMeta(string $key): mixed
    {
        if (!\array_key_exists($key, $this->metadata)) {
            throw new RuntimeException('Metadata key "'.$key.'" does not exist in event '.$this->eventName);
        }

        return $this->metadata[$key];
    }

    /**
     * Get $key from message metadata or default in case key does not exist.
     */
    public function getMetaOrDefault(string $key, mixed $default): mixed
    {
        if (!\array_key_exists($key, $this->metadata)) {
            return $default;
        }

        return $this->metadata[$key];
    }

    /**
     * @param array<mixed> $payload
     *
     * @throws BadMethodCallException always!
     */
    public function withPayload(
        array $payload,
        Schema $assertion,
        PayloadSchema $payloadSchema,
        TypeSchemaMap $typeSchemaMap
    ): Message {
        throw new BadMethodCallException(__METHOD__.' not supported.');
    }

    /**
     * Should be one of Message::TYPE_COMMAND, Message::TYPE_EVENT or Message::TYPE_QUERY.
     */
    public function messageType(): string
    {
        return self::TYPE_EVENT;
    }

    public function messageName(): string
    {
        return $this->eventName;
    }

    public function uuid(): UuidInterface
    {
        return Uuid::fromString($this->eventId);
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public function withMetadata(array $metadata): Message
    {
        $copy = clone $this;
        $copy->metadata = $metadata;

        return $copy;
    }

    /**
     * Returns new instance of message with $key => $value added to metadata.
     *
     * Given value must have a scalar or array type.
     *
     * @param string $key   Key
     * @param mixed  $value Value
     */
    public function withAddedMetadata(string $key, $value): Message
    {
        $copy = clone $this;
        $copy->metadata[$key] = $value;

        return $copy;
    }
}
