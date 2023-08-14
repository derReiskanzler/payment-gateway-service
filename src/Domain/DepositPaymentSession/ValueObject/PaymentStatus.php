<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

use Webmozart\Assert\Assert;

final class PaymentStatus
{
    public const UNPAID = 'unpaid';
    public const PAID = 'paid';

    private const AVAILABLE_PAYMENT_STATUSES = [
        self::UNPAID,
        self::PAID,
    ];

    public static function fromString(string $paymentStatus): self
    {
        return new self($paymentStatus);
    }

    private function __construct(private string $paymentStatus)
    {
        $this->isAvailablePaymentStatus($this->paymentStatus);
    }

    public function toString(): string
    {
        return $this->paymentStatus;
    }

    /**
     * @param string $match entry of AVAILABLE_PAYMENT_STATUSES
     */
    public function matches(string $match): bool
    {
        $this->isAvailablePaymentStatus($match);

        return $this->paymentStatus === $match;
    }

    private function isAvailablePaymentStatus(string $match): void
    {
        Assert::inArray($match, self::AVAILABLE_PAYMENT_STATUSES);
    }
}
