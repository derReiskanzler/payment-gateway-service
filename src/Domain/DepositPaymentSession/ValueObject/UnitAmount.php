<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class UnitAmount
{
    public static function fromFloat(?float $amount): self
    {
        return new self($amount);
    }

    private function __construct(private ?float $amount)
    {
    }

    public function toFloat(): ?float
    {
        return $this->amount;
    }

    public function toCents(): float
    {
        return $this->amount * 100;
    }
}
