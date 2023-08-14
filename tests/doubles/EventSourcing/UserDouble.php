<?php

declare(strict_types=1);

namespace Tests\doubles\EventSourcing;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\ImmutableState;
use EventEngine\Data\ImmutableRecordLogic;
use EventEngine\Persistence\DeletableState;

final class UserDouble implements DeletableState, ImmutableState
{
    use ImmutableRecordLogic;

    public const USER_ID = 'userId';
    public const NAME = 'name';
    public const DELETED = 'deleted';

    private UserIdDouble $userId;

    private UserNameDouble $name;

    private bool $deleted = false;

    public function userId(): UserIdDouble
    {
        return $this->userId;
    }

    public function name(): UserNameDouble
    {
        return $this->name;
    }

    public function deleted(): bool
    {
        return $this->deleted;
    }
}
