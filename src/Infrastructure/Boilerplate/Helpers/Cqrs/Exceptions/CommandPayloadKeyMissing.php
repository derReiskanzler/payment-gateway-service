<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Exceptions;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandName;
use InvalidArgumentException;

class CommandPayloadKeyMissing extends InvalidArgumentException
{
    public static function withKeyOfCommand(string $key, CommandName $commandName): self
    {
        return new self(sprintf('Missing key "%s" in payload of command %s', $key, $commandName));
    }
}
