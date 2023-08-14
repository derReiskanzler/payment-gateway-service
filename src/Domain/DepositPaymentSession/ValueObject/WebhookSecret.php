<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class WebhookSecret
{
    public static function fromString(string $secret): self
    {
        return new self($secret);
    }

    private function __construct(private string $secret)
    {
    }

    public function toString(): string
    {
        return $this->secret;
    }
}
