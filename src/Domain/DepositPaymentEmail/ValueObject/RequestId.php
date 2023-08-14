<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentEmail\ValueObject;

final class RequestId
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
