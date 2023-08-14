<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing;

use EventEngine\Data\ImmutableRecord;

interface DomainEvent extends ImmutableRecord
{
    public static function eventName(): string;
}
