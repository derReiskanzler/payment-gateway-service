<?php

declare(strict_types=1);

namespace Tests\doubles;

use Allmyhomes\EventProjections\Contracts\EventHandlers\EventHandlerInterface;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;

class EventHandlerDouble implements EventHandlerInterface
{
    public function handle(EventDTO $event): void
    {
    }
}
