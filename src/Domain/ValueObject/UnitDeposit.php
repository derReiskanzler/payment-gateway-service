<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\ValueObject;

final class UnitDeposit
{
    public static function fromFloat(float $deposit): self
    {
        return new self($deposit);
    }

    private function __construct(private float $deposit)
    {
    }

    public function toFloat(): float
    {
        return $this->deposit;
    }

    public function toCents(): float
    {
        return $this->deposit * 100;
    }
}
