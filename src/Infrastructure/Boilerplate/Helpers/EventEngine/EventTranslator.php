<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer\EventPayloadTranslatorFactory;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer\EventPayloadTranslatorInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateType;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateVersion;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\CreatedAt;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventEnvelope;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventName;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\EventNameMappingMissing;
use function array_search;
use EventEngine\Messaging\GenericEvent;
use Illuminate\Contracts\Container\BindingResolutionException;

final class EventTranslator
{
    private EventPayloadTranslatorInterface $eventPayloadTranslator;

    /**
     * EventTranslator constructor.
     *
     * @param array<string, string> $eventClassMap Event Class Map; key=event name; value=class
     *
     * @throws BindingResolutionException if nothing is bound to EventPayloadTranslatorInterface
     */
    public function __construct(
        private array $eventClassMap
    ) {
        $this->eventPayloadTranslator = EventPayloadTranslatorFactory::make();
    }

    public function translateFromDomainToGeneric(EventEnvelope $eventEnvelope): GenericEvent
    {
        $message = EventEngineEvent::fromEventEnvelope($eventEnvelope);

        /*
         * @noinspection PhpIncompatibleReturnTypeInspection
         */
        return GenericEvent::fromArray([
            'uuid' => $message->uuid()->toString(),
            'message_name' => $message->messageName(),
            'metadata' => $message->metadata(),
            'created_at' => $message->createdAt(),
            'payload' => $this->eventPayloadTranslator->getPayloadToGenericEvent($message),
        ]);
    }

    public function translateFromGenericToDomain(GenericEvent $genericEvent): EventEnvelope
    {
        if (!\array_key_exists($genericEvent->messageName(), $this->eventClassMap)) {
            throw EventNameMappingMissing::forName(EventName::fromString($genericEvent->messageName()));
        }

        /** @var DomainEvent $eventClass */
        $eventClass = $this->eventClassMap[$genericEvent->messageName()];

        $metadata = $genericEvent->metadata();

        unset($metadata[GenericEvent::META_AGGREGATE_ID], $metadata[GenericEvent::META_AGGREGATE_VERSION], $metadata[GenericEvent::META_AGGREGATE_TYPE]);

        return EventEnvelope::reconstitute(
            EventId::fromString($genericEvent->uuid()->toString()),
            $eventClass::fromArray($this->eventPayloadTranslator->getPayloadToDomainEvent($genericEvent)),
            EventName::fromString($genericEvent->messageName()),
            AggregateId::fromString($genericEvent->getMeta(GenericEvent::META_AGGREGATE_ID)),
            AggregateVersion::fromInt($genericEvent->getMeta(GenericEvent::META_AGGREGATE_VERSION)),
            AggregateType::fromString($genericEvent->getMeta(GenericEvent::META_AGGREGATE_TYPE)),
            $metadata,
            CreatedAt::fromDateTime($genericEvent->createdAt())
        );
    }

    /**
     * @param DomainEvent $event Domain Event
     */
    public function nameOf(DomainEvent $event): EventName
    {
        $name = array_search(\get_class($event), $this->eventClassMap, true);

        if (false === $name) {
            throw EventNameMappingMissing::forEvent($event);
        }

        return EventName::fromString($name);
    }
}
