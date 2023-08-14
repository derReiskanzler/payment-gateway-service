<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\ValueObject;

final class TotalUnitDeposit
{
    public static function fromFloat(float $totalDeposit): self
    {
        return new self($totalDeposit);
    }

    private function __construct(private float $totalDeposit)
    {
    }

    public function toFloat(): float
    {
        return $this->totalDeposit;
    }

    public function isEmpty(): bool
    {
        return empty($this->totalDeposit);
    }
}
