<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;

final class AggregateStateClass
{
    use toString;

    public static function fromString(string $stateClass): self
    {
        return new self($stateClass);
    }

    private function __construct(private string $stateClass)
    {
    }

    public function toString(): string
    {
        return $this->stateClass;
    }
}
