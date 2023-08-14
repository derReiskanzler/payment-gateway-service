<?php

declare(strict_types=1);

namespace Tests\doubles\EventSourcing;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\ImmutableEventTrait;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;

final class UserRegisteredWithOccurredAtDouble implements DomainEvent
{
    use ImmutableEventTrait;

    public const USER_ID = 'user_id';
    public const NAME = 'name';

    private UserIdDouble $userId;

    private UserNameDouble $name;

    public static function eventName(): string
    {
        return 'AppTest.UserRegistered';
    }

    public function userId(): UserIdDouble
    {
        return $this->userId;
    }

    public function name(): UserNameDouble
    {
        return $this->name;
    }
}
