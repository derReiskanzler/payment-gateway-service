<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\ValueObject;

final class UnitName
{
    public static function fromString(string $unitName): self
    {
        return new self($unitName);
    }

    private function __construct(private string $unitName)
    {
    }

    public function toString(): string
    {
        return $this->unitName;
    }
}
