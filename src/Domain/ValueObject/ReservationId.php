<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\ValueObject;

final class ReservationId
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

    /**
     * return Stringable when accessing AggregateId for DepositPaymentSession Aggregate.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
