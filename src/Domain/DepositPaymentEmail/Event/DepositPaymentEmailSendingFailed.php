<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentEmail\Event;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\ImmutableEventTrait;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;

final class DepositPaymentEmailSendingFailed implements DomainEvent
{
    use ImmutableEventTrait;

    private const EVENT_NAME = Context::NAME.'DepositPaymentEmailSendingFailed';

    public const PROSPECT_ID = 'prospectId';
    public const RESERVATION_ID = 'reservationId';
    public const CHECKOUT_SESSION_ID = 'checkoutSessionId';
    public const CHECKOUT_SESSION_URL = 'checkoutSessionUrl';
    public const EXPIRES_AT = 'expiresAt';
    public const ERROR_COUNT = 'errorCount';
    public const CREATED_AT = 'createdAt';

    private ProspectId $prospectId;
    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private CheckoutSessionUrl $checkoutSessionUrl;
    private ExpiresAt $expiresAt;
    private ErrorCount $errorCount;
    private CreatedAt $createdAt;

    public static function eventName(): string
    {
        return self::EVENT_NAME;
    }

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

    public function checkoutSessionUrl(): CheckoutSessionUrl
    {
        return $this->checkoutSessionUrl;
    }

    public function expiresAt(): ExpiresAt
    {
        return $this->expiresAt;
    }

    public function errorCount(): ErrorCount
    {
        return $this->errorCount;
    }

    public function createdAt(): CreatedAt
    {
        return $this->createdAt;
    }
}
