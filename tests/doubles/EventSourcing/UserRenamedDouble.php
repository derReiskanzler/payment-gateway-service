<?php

declare(strict_types=1);

namespace Tests\doubles\EventSourcing;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;
use EventEngine\Data\ImmutableRecordLogic;

final class UserRenamedDouble implements DomainEvent
{
    use ImmutableRecordLogic;

    public const USER_ID = 'userId';
    public const NEW_NAME = 'newName';

    private UserIdDouble $userId;

    private UserNameDouble $newName;

    public static function eventName(): string
    {
        return 'AppUnitTest.UserRenamed';
    }

    public function userId(): UserIdDouble
    {
        return $this->userId;
    }

    public function newName(): UserNameDouble
    {
        return $this->newName;
    }
}
