<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;
use Exception;
use InvalidArgumentException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class EventId
{
    use toString;

    /**
     * @throws UnsatisfiedDependencyException if `Moontoast\Math\BigNumber` is not present
     * @throws InvalidArgumentException       if the uuid-generator is not configured correctly
     * @throws Exception                      if it was not possible to gather sufficient entropy for the uuid-generator
     */
    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    /**
     * @throws InvalidUuidStringException if $id is not a valid Uuid
     */
    public static function fromString(string $id): self
    {
        return new self(Uuid::fromString($id));
    }

    private function __construct(private UuidInterface $id)
    {
    }

    public function toString(): string
    {
        return $this->id->toString();
    }
}
