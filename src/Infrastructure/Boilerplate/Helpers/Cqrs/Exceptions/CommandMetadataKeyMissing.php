<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Exceptions;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandName;
use InvalidArgumentException;

class CommandMetadataKeyMissing extends InvalidArgumentException
{
    public static function withKeyOfCommand(string $key, CommandName $commandName): self
    {
        return new self(sprintf('Missing key "%s" in metadata of command %s', $key, $commandName));
    }
}
