<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\MetadataEnrichers;

use EventEngine\Messaging\Message;

interface MetadataEnricherInterface
{
    /**
     * Return the given message with added metadata.
     *
     * @param Message $message Message
     */
    public function enrich(Message $message): Message;
}
