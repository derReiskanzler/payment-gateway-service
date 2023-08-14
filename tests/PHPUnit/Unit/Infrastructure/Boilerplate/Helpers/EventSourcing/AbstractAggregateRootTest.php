<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\EventSourcing;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateType;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateVersion;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventEnvelope;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventName;
use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Tests\doubles\EventSourcing\UserBehaviorDouble;
use Tests\doubles\EventSourcing\UserDouble;
use Tests\doubles\EventSourcing\UserIdDouble;
use Tests\doubles\EventSourcing\UserNameDouble;
use Tests\doubles\EventSourcing\UserRegisteredDouble;

class AbstractAggregateRootTest extends TestCase
{
    public function testAggregateCanBeConstructedAndRecordsEvent(): void
    {
        $userId = UserIdDouble::generate();
        $userName = UserNameDouble::fromString('Jane Doe');

        $user = UserBehaviorDouble::register($userId, $userName);
        /** @var UserDouble $state */
        $state = $user->state();

        static::assertSame('Jane Doe', $state->name()->toString());
        static::assertSame(1, $user->version()->toInt());

        $recordedEvents = $user->popRecordedEvents();

        static::assertCount(1, $recordedEvents);
        static::assertCount(0, $user->popRecordedEvents());
    }

    public function testAggregateCanBeReconstitutedFromHistory(): void
    {
        $userId = UserIdDouble::generate();

        $pastEvent = EventEnvelope::wrap(
            UserRegisteredDouble::fromArray([
                UserRegisteredDouble::USER_ID => $userId->toString(),
                UserRegisteredDouble::NAME => 'Jane Doe',
            ]),
            EventName::fromString(UserRegisteredDouble::eventName()),
            AggregateId::fromValueObject($userId),
            AggregateVersion::fromInt(1),
            AggregateType::fromString('AppUnitTest.User')
        );

        $user = UserBehaviorDouble::reconstituteFromHistory(new ArrayIterator([$pastEvent]));
        /** @var UserDouble $state */
        $state = $user->state();

        static::assertSame('Jane Doe', $state->name()->toString());
        static::assertSame(1, $user->version()->toInt());
    }

    public function testAggregateCanBeReconstitutedFromState(): void
    {
        $state = UserDouble::fromArray([
            UserDouble::USER_ID => UserIdDouble::generate()->toString(),
            UserDouble::NAME => 'Jane Doe',
        ]);

        $user = UserBehaviorDouble::reconstituteFromAggregateState($state, AggregateVersion::fromInt(1));

        /** @var UserDouble $state */
        $state = $user->state();

        static::assertSame('Jane Doe', $state->name()->toString());
        static::assertSame(1, $user->version()->toInt());
    }

    public function testAggregateCanRecordAFollowUpEvent(): void
    {
        $userId = UserIdDouble::generate();

        $pastEvent = EventEnvelope::wrap(
            UserRegisteredDouble::fromArray([
                UserRegisteredDouble::USER_ID => $userId->toString(),
                UserRegisteredDouble::NAME => 'Jane Doe',
            ]),
            EventName::fromString(UserRegisteredDouble::eventName()),
            AggregateId::fromValueObject($userId),
            AggregateVersion::fromInt(1),
            AggregateType::fromString('AppUnitTest.User')
        );

        $user = UserBehaviorDouble::reconstituteFromHistory(new ArrayIterator([$pastEvent]));
        $user->rename(UserNameDouble::fromString('Jane Smith'));
        /** @var UserDouble $state */
        $state = $user->state();

        static::assertSame('Jane Smith', $state->name()->toString());
        static::assertSame(2, $user->version()->toInt());

        $recordedEvents = $user->popRecordedEvents();

        static::assertCount(1, $recordedEvents);
        static::assertCount(0, $user->popRecordedEvents());
    }
}
