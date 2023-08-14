<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Prooph\Messaging;

use Prooph\Common\Messaging\DomainMessage;
use Prooph\Common\Messaging\Message;

class GenericDomainMessage extends DomainMessage
{
    /**
     * @var array<string, mixed>
     */
    private array $payload = [];

    public function messageType(): string
    {
        return Message::TYPE_EVENT;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }
}
