<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class Quantity
{
    public static function fromInt(int $quantity): self
    {
        return new self($quantity);
    }

    private function __construct(private int $quantity)
    {
    }

    public function toInt(): int
    {
        return $this->quantity;
    }
}
