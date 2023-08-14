<?php

declare(strict_types=1);

namespace Tests\doubles\EventSourcing;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\AbstractAggregateRoot;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\ImmutableState;

final class UserBehaviorDouble extends AbstractAggregateRoot
{
    protected ImmutableState|UserDouble $state;

    /**
     * @param UserIdDouble   $userId   User Id
     * @param UserNameDouble $userName User Name
     *
     * @return static
     */
    public static function register(UserIdDouble $userId, UserNameDouble $userName): self
    {
        $instance = new self();
        $instance->recordThat(UserRegisteredDouble::fromRecordData([
            UserRegisteredDouble::USER_ID => $userId,
            UserRegisteredDouble::NAME => $userName,
        ]));

        return $instance;
    }

    /**
     * @param UserIdDouble   $userId   User Id
     * @param UserNameDouble $userName User Name
     *
     * @return static
     */
    public static function registerAndRename(UserIdDouble $userId, UserNameDouble $userName): self
    {
        $instance = new self();
        $instance->recordThat(UserRegisteredDouble::fromRecordData([
            UserRegisteredDouble::USER_ID => $userId,
            UserRegisteredDouble::NAME => UserNameDouble::fromString('Noname'),
        ]));
        $instance->recordThat(UserRenamedDouble::fromRecordData([
            UserRenamedDouble::USER_ID => $userId,
            UserRenamedDouble::NEW_NAME => $userName,
        ]));

        return $instance;
    }

    public function aggregateId(): AggregateId
    {
        return AggregateId::fromString($this->state->userId()->toString());
    }

    /**
     * @param UserRegisteredDouble $event Event
     */
    public function whenUserRegisteredDouble(UserRegisteredDouble $event): UserDouble
    {
        return UserDouble::fromArray($event->toArray());
    }

    public function whenUserRegisteredWithOccurredAtDouble(UserRegisteredWithOccurredAtDouble $event): UserDouble
    {
        return UserDouble::fromArray([
            UserDouble::USER_ID => $event->userId()->toString(),
            UserDouble::NAME => $event->name()->toString(),
        ]);
    }

    /**
     * @param UserNameDouble $newName New Name
     */
    public function rename(UserNameDouble $newName): void
    {
        if ($this->state->name()->equals($newName)) {
            return;
        }

        $this->recordThat(UserRenamedDouble::fromRecordData([
            UserRenamedDouble::USER_ID => $this->state->userId(),
            UserRenamedDouble::NEW_NAME => $newName,
        ]));
    }

    /**
     * @param UserRenamedDouble $event Event
     */
    public function whenUserRenamedDouble(UserRenamedDouble $event): UserDouble
    {
        return $this->state->with([
            UserDouble::NAME => $event->newName(),
        ]);
    }

    public function delete(): void
    {
        $this->recordThat(UserDeletedDouble::fromRecordData([
            UserDeletedDouble::USER_ID => $this->state->userId(),
        ]));
    }

    public function whenUserDeletedDouble(UserDeletedDouble $event): UserDouble
    {
        return $this->state->with([UserDouble::DELETED => true]);
    }
}
