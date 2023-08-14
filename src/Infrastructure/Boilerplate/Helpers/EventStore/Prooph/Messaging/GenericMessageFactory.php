<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Prooph\Messaging;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageFactory;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;

class GenericMessageFactory implements MessageFactory
{
    /**
     * @param array<string, string|DateTime|array<mixed>> $messageData
     *
     * @throws UnsatisfiedDependencyException if `Moontoast\Math\BigNumber` is not present
     * @throws InvalidArgumentException       if the uuid-generator is not configured correctly
     * @throws Exception                      if it was not possible to gather sufficient entropy for the uuid-generator
     */
    public function createMessageFromArray(string $messageName, array $messageData): Message
    {
        if (!isset($messageData['message_name'])) {
            $messageData['message_name'] = $messageName;
        }

        if (!isset($messageData['uuid'])) {
            $messageData['uuid'] = Uuid::uuid4();
        }

        if (!isset($messageData['created_at'])) {
            $messageData['created_at'] = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        }

        if (!isset($messageData['metadata'])) {
            $messageData['metadata'] = [];
        }

        return GenericDomainMessage::fromArray($messageData);
    }
}
