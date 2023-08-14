<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\MetadataEnrichers;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\System\Clock\Clock;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\System\Clock\SystemClock;
use EventEngine\Messaging\Message;

class DefaultMetadataEnricher implements MetadataEnricherInterface
{
    /**
     * Return the given message with added metadata.
     */
    public function enrich(Message $message): Message
    {
        $message = $message->withAddedMetadata('event_id', $message->uuid()->toString());
        $message = $message->withAddedMetadata('owning_app', $this->getServiceName());

        return $message->withAddedMetadata('occurred_at', $this->getOccurredAt());
    }

    private function getServiceName(): string
    {
        $serviceName = str_replace('-', '_', config('app.app_name'));

        return strtolower($serviceName);
    }

    private function getOccurredAt(): string
    {
        return $this->getClock()->now()->format('Y-m-d\TH:i:s.u');
    }

    private function getClock(): Clock
    {
        return new SystemClock();
    }
}
