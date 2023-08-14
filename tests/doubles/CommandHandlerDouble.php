<?php

declare(strict_types=1);

namespace Tests\doubles;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandHandler;

class CommandHandlerDouble implements CommandHandler
{
    public function handleCommandDouble(CommandDouble $command): string
    {
        return $command->getRoomSize();
    }
}
