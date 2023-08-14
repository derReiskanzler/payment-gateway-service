<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\ImmutableState;
use EventEngine\Util\VariableType;
use InvalidArgumentException;
use function sprintf;

final class InvalidReturnType extends InvalidArgumentException
{
    public static function ofWhenFunction(string $aggregateType, string $whenFunction, mixed $returnValue): self
    {
        return new self(sprintf(
            'When function "%s" of Aggregate %s did not return a %s. Got "%s" instead.',
            $whenFunction,
            $aggregateType,
            ImmutableState::class,
            VariableType::determine($returnValue)
        ));
    }
}
