<?php

declare(strict_types=1);

namespace Tests\doubles\EventSourcing;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventTranslator;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\MultiModelStoreAggregateRepository;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateTypeMap;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\ImmutableState;
use Throwable;

final class UserRepositoryDouble extends MultiModelStoreAggregateRepository
{
    /**
     * @param UserBehaviorDouble $user    User Behavior
     * @param Command            $command Command
     *
     * @throws Throwable
     */
    public function save(UserBehaviorDouble $user, Command $command): void
    {
        $this->saveAggregate($user, $command);
    }

    /**
     * @param UserIdDouble $userId User Id
     */
    public function get(UserIdDouble $userId): UserBehaviorDouble
    {
        return $this->getAggregate(AggregateId::fromValueObject($userId));
    }

    /**
     * @param UserIdDouble $userId User Id
     */
    public function getEvents(UserIdDouble $userId): iterable
    {
        return $this->getAggregateEvents(AggregateId::fromValueObject($userId));
    }

    /**
     * @param UserIdDouble $userId User Id
     */
    public function getUntilVersion(UserIdDouble $userId, int $maxVersion): UserBehaviorDouble
    {
        return $this->getAggregateUntilVersion(AggregateId::fromValueObject($userId), $maxVersion);
    }

    protected function deriveAggregateMetadataFromState(ImmutableState $state): ?array
    {
        /* @var UserDouble $state */
        return [
            'name' => $state->name()->toString(),
        ];
    }

    protected function getAggregateTypeMap(): AggregateTypeMap
    {
        return AggregateTypeMap::fromArray([
            AggregateTypeMap::AGGREGATE_TYPE => 'AppUnitTest.User',
            AggregateTypeMap::AGGREGATE_BEHAVIOR_CLASS => UserBehaviorDouble::class,
            AggregateTypeMap::AGGREGATE_STATE_CLASS => UserDouble::class,
        ]);
    }

    protected function getEventTranslatorMap(): EventTranslator
    {
        return new EventTranslator([
            UserRegisteredDouble::eventName() => UserRegisteredDouble::class,
            UserRenamedDouble::eventName() => UserRenamedDouble::class,
            UserDeletedDouble::eventName() => UserDeletedDouble::class,
        ]);
    }

    protected function getStreamName(): string
    {
        return 'users-stream';
    }

    protected function getCollectionName(): string
    {
        return 'users';
    }
}
