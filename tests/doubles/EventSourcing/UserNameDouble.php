<?php

declare(strict_types=1);

namespace Tests\doubles\EventSourcing;

final class UserNameDouble
{
    private string $name;

    /**
     * @return static
     */
    public static function fromString(string $name): self
    {
        return new self($name);
    }

    /**
     * UserNameDouble constructor.
     */
    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public function toString(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $other Other VO
     */
    public function equals(mixed $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->name === $other->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
