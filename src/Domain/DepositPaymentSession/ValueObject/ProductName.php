<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class ProductName
{
    public static function fromString(?string $name): self
    {
        return new self($name);
    }

    private function __construct(private ?string $name)
    {
    }

    public function toString(): ?string
    {
        return $this->name;
    }
}
