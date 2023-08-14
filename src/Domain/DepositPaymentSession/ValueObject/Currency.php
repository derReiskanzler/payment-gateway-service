<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class Currency
{
    public static function fromString(string $currency): self
    {
        return new self($currency);
    }

    private function __construct(private string $currency)
    {
    }

    public function toString(): string
    {
        return $this->currency;
    }
}
