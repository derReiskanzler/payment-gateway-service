<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventEngineEvent;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateType;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateVersion;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventEnvelope;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventName;
use EventEngine\Messaging\Exception\RuntimeException;
use EventEngine\Messaging\GenericEvent;
use PHPUnit\Framework\TestCase;
use Tests\doubles\EventSourcing\UserIdDouble;
use Tests\doubles\EventSourcing\UserRegisteredDouble;

class EventEngineEventTest extends TestCase
{
    public function testEventEngineEventCanBeConstructedFromEventEnvelope(): void
    {
        $userId = UserIdDouble::generate();

        $envelope = $this->getUserRegisteredEnvelope($userId);

        $eeEvent = EventEngineEvent::fromEventEnvelope($envelope);

        static::assertSame($userId->toString(), $eeEvent->get(UserRegisteredDouble::USER_ID));
        static::assertSame($envelope->eventId()->toString(), $eeEvent->uuid()->toString());
        static::assertSame($envelope->eventName()->toString(), $eeEvent->messageName());
        static::assertSame(
            $envelope->aggregateType()->toString(),
            $eeEvent->getMeta(GenericEvent::META_AGGREGATE_TYPE)
        );
        static::assertSame(
            $envelope->aggregateId()->toString(),
            $eeEvent->getMeta(GenericEvent::META_AGGREGATE_ID)
        );
        static::assertSame(
            $envelope->aggregateVersion()->toInt(),
            $eeEvent->getMeta(GenericEvent::META_AGGREGATE_VERSION)
        );
        static::assertSame('meta', $eeEvent->getMeta('additional'));
    }

    public function testEventUsesDefaultIfPayloadOrMetaKeyDoesNotExistAndDefaultIsProvided(): void
    {
        $userId = UserIdDouble::generate();

        $envelope = $this->getUserRegisteredEnvelope($userId);

        $eeEvent = EventEngineEvent::fromEventEnvelope($envelope);

        static::assertSame('some default', $eeEvent->getOrDefault('nokey', 'some default'));
        static::assertSame('some default', $eeEvent->getMetaOrDefault('nometa', 'some default'));
    }

    public function testEventThrowsExceptionIfPayloadOrMetaKeyDoesNotExist(): void
    {
        $userId = UserIdDouble::generate();

        $envelope = $this->getUserRegisteredEnvelope($userId);

        $eeEvent = EventEngineEvent::fromEventEnvelope($envelope);

        $this->expectException(RuntimeException::class);
        $eeEvent->get('nokey');

        $this->expectException(RuntimeException::class);
        $eeEvent->getMeta('nometa');
    }

    public function testEventMetadataIsImmutable(): void
    {
        $userId = UserIdDouble::generate();

        $envelope = $this->getUserRegisteredEnvelope($userId);

        $eeEvent = EventEngineEvent::fromEventEnvelope($envelope);

        $changedEvent = $eeEvent->withMetadata(['new' => 'value']);

        static::assertSame('meta', $eeEvent->getMeta('additional'));
        static::assertSame(['new' => 'value'], $changedEvent->metadata());

        $changedEvent = $changedEvent->withAddedMetadata('another', 'value');

        static::assertSame(['new' => 'value', 'another' => 'value'], $changedEvent->metadata());
    }

    private function getUserRegisteredEnvelope(UserIdDouble $userId): EventEnvelope
    {
        return EventEnvelope::wrap(
            UserRegisteredDouble::fromArray([
                UserRegisteredDouble::USER_ID => $userId->toString(),
                UserRegisteredDouble::NAME => 'Jane Doe',
            ]),
            EventName::fromString(UserRegisteredDouble::eventName()),
            AggregateId::fromValueObject($userId),
            AggregateVersion::fromInt(1),
            AggregateType::fromString('AppUnitTest.User')
        )->withAddedMetadata('additional', 'meta');
    }
}
