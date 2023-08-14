<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;

final class AggregateBehaviorClass
{
    use toString;

    public static function fromString(string $aggregateClass): self
    {
        return new self($aggregateClass);
    }

    private function __construct(private string $aggregateClass)
    {
    }

    public function toString(): string
    {
        return $this->aggregateClass;
    }
}
