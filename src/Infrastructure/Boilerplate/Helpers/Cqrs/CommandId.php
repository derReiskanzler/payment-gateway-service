<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;
use Exception;
use InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class CommandId
{
    use toString;

    private UuidInterface $id;

    /**
     * @throws UnsatisfiedDependencyException if `Moontoast\Math\BigNumber` is not present
     * @throws InvalidArgumentException       if the uuid-generator is not configured correctly
     * @throws Exception                      if it was not possible to gather sufficient entropy for the uuid-generator
     *
     * @deprecated Please don't use it and should be replaced with Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGeneratorInterface
     */
    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $id): self
    {
        return new self(Uuid::fromString($id));
    }

    private function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    public function toString(): string
    {
        return $this->id->toString();
    }
}
