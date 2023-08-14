<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\Aggregate;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\ImmutableState;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\ImmutableRecordLogicTrait;

final class DepositPaymentSessionState implements ImmutableState
{
    use ImmutableRecordLogicTrait;

    public const RESERVATION_ID = 'reservationId';
    public const CHECKOUT_SESSION_ID = 'checkoutSessionId';
    public const CHECKOUT_SESSION_STATUS = 'checkoutSessionStatus';
    public const PAYMENT_STATUS = 'paymentStatus';
    public const ERROR_COUNT = 'errorCount';

    private ReservationId $reservationId;

    private ?CheckoutSessionId $checkoutSessionId;

    private ?CheckoutSessionStatus $checkoutSessionStatus;

    private ?PaymentStatus $paymentStatus;

    private ErrorCount $errorCount;

    public function reservationId(): ReservationId
    {
        return $this->reservationId;
    }

    public function checkoutSessionId(): ?CheckoutSessionId
    {
        return $this->checkoutSessionId;
    }

    public function checkoutSessionStatus(): ?CheckoutSessionStatus
    {
        return $this->checkoutSessionStatus;
    }

    public function paymentStatus(): ?PaymentStatus
    {
        return $this->paymentStatus;
    }

    public function errorCount(): ErrorCount
    {
        return $this->errorCount;
    }
}
