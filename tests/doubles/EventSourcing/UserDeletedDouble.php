<?php

declare(strict_types=1);

namespace Tests\doubles\EventSourcing;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;
use EventEngine\Data\ImmutableRecordLogic;

final class UserDeletedDouble implements DomainEvent
{
    use ImmutableRecordLogic;

    public const USER_ID = 'userId';

    private UserIdDouble $userId;

    public static function eventName(): string
    {
        return 'AppUnitTest.UserDeleted';
    }

    public function userId(): UserIdDouble
    {
        return $this->userId;
    }
}
