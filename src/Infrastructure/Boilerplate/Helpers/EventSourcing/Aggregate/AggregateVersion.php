<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;

final class AggregateVersion
{
    use toString;

    public static function zero(): self
    {
        return new self(0);
    }

    public static function fromInt(int $version): self
    {
        return new self($version);
    }

    private function __construct(private int $version)
    {
    }

    public function increase(): self
    {
        return new self($this->version + 1);
    }

    public function subtract(int $number): self
    {
        return new self($this->version - $number);
    }

    public function toInt(): int
    {
        return $this->version;
    }

    public function toString(): string
    {
        return (string) $this->version;
    }
}
