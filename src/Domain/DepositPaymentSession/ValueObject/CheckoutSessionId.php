<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class CheckoutSessionId
{
    public static function fromString(string $id): self
    {
        return new self($id);
    }

    private function __construct(private string $id)
    {
    }

    public function toString(): string
    {
        return $this->id;
    }
}
