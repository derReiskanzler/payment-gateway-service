<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

use Webmozart\Assert\Assert;

final class CheckoutSessionStatus
{
    public const OPEN = 'open';
    public const COMPLETE = 'complete';
    public const EXPIRED = 'expired';

    private const AVAILABLE_CHECKOUT_STATUSES = [
        self::OPEN,
        self::COMPLETE,
        self::EXPIRED,
    ];

    public static function fromString(string $checkoutSessionStatus): self
    {
        return new self($checkoutSessionStatus);
    }

    private function __construct(private string $checkoutSessionStatus)
    {
        $this->isAvailableStatus($this->checkoutSessionStatus);
    }

    public function toString(): string
    {
        return $this->checkoutSessionStatus;
    }

    /**
     * @param string $match entry of AVAILABLE_CHECKOUT_STATUSES
     */
    public function matches(string $match): bool
    {
        $this->isAvailableStatus($match);

        return $this->checkoutSessionStatus === $match;
    }

    private function isAvailableStatus(string $match): void
    {
        Assert::inArray($match, self::AVAILABLE_CHECKOUT_STATUSES);
    }
}
