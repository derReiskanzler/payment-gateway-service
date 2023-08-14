<?php

declare(strict_types=1);

namespace Tests\doubles\EventSourcing;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;
use EventEngine\Data\ImmutableRecordLogic;

final class UserRegisteredDouble implements DomainEvent
{
    use ImmutableRecordLogic;

    public const USER_ID = 'userId';
    public const NAME = 'name';

    private UserIdDouble $userId;

    private UserNameDouble $name;

    public static function eventName(): string
    {
        return 'AppUnitTest.UserRegistered';
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
