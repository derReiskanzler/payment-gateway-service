<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Events;

use EventEngine\Messaging\GenericEvent;
use Illuminate\Queue\SerializesModels;

class ProophEventStoredInStream
{
    use SerializesModels;

    public function __construct(
        private GenericEvent $proophEvent,
        private string $eventStreamName
    ) {
    }

    public function getProophEvent(): GenericEvent
    {
        return $this->proophEvent;
    }

    public function getEventStreamName(): string
    {
        return $this->eventStreamName;
    }
}
