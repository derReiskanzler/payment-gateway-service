<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\EventSourcing\Event;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateType;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateVersion;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventEnvelope;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventName;
use PHPUnit\Framework\TestCase;
use Tests\doubles\EventSourcing\UserIdDouble;
use Tests\doubles\EventSourcing\UserRegisteredDouble;

class EventEnvelopeTest extends TestCase
{
    public function testEnvelopeWrapsDomainEvent(): void
    {
        $eventEnvelope = $this->getUserRegisteredEnvelope();

        static::assertInstanceOf(UserRegisteredDouble::class, $eventEnvelope->event());
    }

    public function testEnvelopeHandlesMetadata(): void
    {
        $eventEnvelope = $this->getUserRegisteredEnvelope();

        $eventEnvelopeWithMeta = $eventEnvelope->withAddedMetadata('test', 'meta');

        static::assertNull($eventEnvelope->metadata('test'));
        static::assertSame('meta', $eventEnvelopeWithMeta->metadata('test'));

        static::assertSame([
            'test' => 'meta',
        ], $eventEnvelopeWithMeta->metadata());
    }

    private function getUserRegisteredEnvelope(): EventEnvelope
    {
        $userId = UserIdDouble::generate()->toString();

        return EventEnvelope::wrap(
            UserRegisteredDouble::fromArray([
                UserRegisteredDouble::USER_ID => $userId,
                UserRegisteredDouble::NAME => 'Jane Doe',
            ]),
            EventName::fromString(UserRegisteredDouble::eventName()),
            AggregateId::fromString($userId),
            AggregateVersion::fromInt(1),
            AggregateType::fromString('AppUnitTest.User')
        );
    }
}
