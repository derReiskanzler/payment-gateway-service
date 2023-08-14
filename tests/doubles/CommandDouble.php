<?php

declare(strict_types=1);

namespace Tests\doubles;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\AbstractCommand;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandName;

class CommandDouble extends AbstractCommand
{
    public const APARTMENT_SIZE = '100m';

    public function getRoomSize(): string
    {
        return $this->get('room_size');
    }

    public function getApartmentSize(): string
    {
        return $this->getOrDefault('apartment_size', self::APARTMENT_SIZE);
    }

    public function getInvalidPayloadParam(): string
    {
        return $this->get('invalid_param');
    }

    public function commandName(): CommandName
    {
        return CommandName::fromString('CommandDouble');
    }
}
