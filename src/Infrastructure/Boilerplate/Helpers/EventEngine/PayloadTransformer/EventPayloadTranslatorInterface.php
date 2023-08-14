<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer;

use EventEngine\Messaging\Message;

interface EventPayloadTranslatorInterface
{
    /**
     * @return array<string, string>
     */
    public function getPayloadToGenericEvent(Message $message): array;

    /**
     * @return array<string, string>
     */
    public function getPayloadToDomainEvent(Message $message): array;
}
