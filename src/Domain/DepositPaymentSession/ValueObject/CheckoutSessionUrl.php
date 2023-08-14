<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class CheckoutSessionUrl
{
    public static function fromString(string $url): self
    {
        return new self($url);
    }

    private function __construct(private string $url)
    {
    }

    public function toString(): string
    {
        return $this->url;
    }
}
