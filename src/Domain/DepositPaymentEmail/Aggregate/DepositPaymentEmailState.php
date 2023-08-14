<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentEmail\Aggregate;

use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\RequestId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\ImmutableState;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\ImmutableRecordLogicTrait;

final class DepositPaymentEmailState implements ImmutableState
{
    use ImmutableRecordLogicTrait;

    public const PROSPECT_ID = 'prospectId';
    public const RESERVATION_ID = 'reservationId';
    public const CHECKOUT_SESSION_ID = 'checkoutSessionId';
    public const REQUEST_ID = 'requestId';
    public const ERROR_COUNT = 'errorCount';

    private ProspectId $prospectId;
    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private ?RequestId $requestId;
    private ErrorCount $errorCount;

    public function prospectId(): ProspectId
    {
        return $this->prospectId;
    }

    public function reservationId(): ReservationId
    {
        return $this->reservationId;
    }

    public function checkoutSessionId(): CheckoutSessionId
    {
        return $this->checkoutSessionId;
    }

    public function requestId(): ?RequestId
    {
        return $this->requestId;
    }

    public function errorCount(): ErrorCount
    {
        return $this->errorCount;
    }
}
