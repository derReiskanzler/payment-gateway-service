<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Exception;
use InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\UuidFactoryInterface;

final class CommandIdGenerator implements CommandIdGeneratorInterface
{
    public function __construct(private UuidFactoryInterface $uuidFactory)
    {
    }

    /**
     * @throws UnsatisfiedDependencyException if `Moontoast\Math\BigNumber` is not present
     * @throws InvalidArgumentException       if the uuid-generator is not configured correctly
     * @throws Exception                      if it was not possible to gather sufficient entropy for the uuid-generator
     */
    public function generate(): CommandId
    {
        return CommandId::fromString($this->uuidFactory->uuid4()->toString());
    }
}
