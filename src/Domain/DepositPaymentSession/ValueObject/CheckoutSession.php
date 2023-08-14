<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

final class CheckoutSession
{
    public const CHECKOUT_SESSION_ID = 'checkout_session_id';
    public const CHECKOUT_SESSION_STATUS = 'checkout_session_status';
    public const CHECKOUT_SESSION_URL = 'checkout_session_url';
    public const CURRENCY = 'currency';
    public const CUSTOMER_ID = 'customer_id';
    public const EXPIRES_AT = 'expires_at';
    public const PAYMENT_INTENT_ID = 'payment_intent_id';
    public const PAYMENT_STATUS = 'payment_status';

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            CheckoutSessionId::fromString($data[self::CHECKOUT_SESSION_ID]),
            $data[self::CHECKOUT_SESSION_STATUS] ? CheckoutSessionStatus::fromString($data[self::CHECKOUT_SESSION_STATUS]) : null,
            $data[self::CHECKOUT_SESSION_URL] ? CheckoutSessionUrl::fromString($data[self::CHECKOUT_SESSION_URL]) : null,
            $data[self::CURRENCY] ? Currency::fromString($data[self::CURRENCY]) : null,
            $data[self::CUSTOMER_ID] ? CustomerId::fromString($data[self::CUSTOMER_ID]) : null,
            ExpiresAt::fromSeconds($data[self::EXPIRES_AT]),
            $data[self::PAYMENT_INTENT_ID] ? PaymentIntentId::fromString($data[self::PAYMENT_INTENT_ID]) : null,
            PaymentStatus::fromString($data[self::PAYMENT_STATUS]),
        );
    }

    private function __construct(
        private CheckoutSessionId $id,
        private ?CheckoutSessionStatus $status,
        private ?CheckoutSessionUrl $url,
        private ?Currency $currency,
        private ?CustomerId $customerId,
        private ExpiresAt $expiresAt,
        private ?PaymentIntentId $paymentIntentId,
        private PaymentStatus $paymentStatus,
    ) {
    }

    public function id(): ?CheckoutSessionId
    {
        return $this->id;
    }

    public function status(): ?CheckoutSessionStatus
    {
        return $this->status;
    }

    public function url(): ?CheckoutSessionUrl
    {
        return $this->url;
    }

    public function currency(): ?Currency
    {
        return $this->currency;
    }

    public function customerId(): ?CustomerId
    {
        return $this->customerId;
    }

    public function expiresAt(): ExpiresAt
    {
        return $this->expiresAt;
    }

    public function paymentIntentId(): ?PaymentIntentId
    {
        return $this->paymentIntentId;
    }

    public function paymentStatus(): PaymentStatus
    {
        return $this->paymentStatus;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::CHECKOUT_SESSION_ID => $this->id->toString(),
            self::CHECKOUT_SESSION_STATUS => $this->status?->toString(),
            self::CHECKOUT_SESSION_URL => $this->url?->toString(),
            self::CURRENCY => $this->currency?->toString(),
            self::CUSTOMER_ID => $this->customerId?->toString(),
            self::EXPIRES_AT => $this->expiresAt->toString(),
            self::PAYMENT_INTENT_ID => $this->paymentIntentId?->toString(),
            self::PAYMENT_STATUS => $this->paymentStatus->toString(),
        ];
    }
}
