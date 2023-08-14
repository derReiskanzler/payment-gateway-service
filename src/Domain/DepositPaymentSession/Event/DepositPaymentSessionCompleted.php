<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\Event;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\ImmutableEventTrait;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;

final class DepositPaymentSessionCompleted implements DomainEvent
{
    use ImmutableEventTrait;

    private const EVENT_NAME = Context::NAME.'DepositPaymentSessionCompleted';

    public const RESERVATION_ID = 'reservationId';
    public const CHECKOUT_SESSION_ID = 'checkoutSessionId';
    public const STATUS = 'status';
    public const PAYMENT_STATUS = 'paymentStatus';
    public const CREATED_AT = 'createdAt';

    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private CheckoutSessionStatus $status;
    private PaymentStatus $paymentStatus;
    private CreatedAt $createdAt;

    public static function eventName(): string
    {
        return self::EVENT_NAME;
    }

    public function reservationId(): ReservationId
    {
        return $this->reservationId;
    }

    public function checkoutSessionId(): CheckoutSessionId
    {
        return $this->checkoutSessionId;
    }

    public function status(): CheckoutSessionStatus
    {
        return $this->status;
    }

    public function paymentStatus(): PaymentStatus
    {
        return $this->paymentStatus;
    }

    public function createdAt(): CreatedAt
    {
        return $this->createdAt;
    }
}
