<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentEmail\Event;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\RequestId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\ImmutableEventTrait;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;

final class DepositPaymentEmailSentToProspect implements DomainEvent
{
    use ImmutableEventTrait;

    private const EVENT_NAME = Context::NAME.'DepositPaymentEmailSentToProspect';

    public const PROSPECT_ID = 'prospectId';
    public const RESERVATION_ID = 'reservationId';
    public const CHECKOUT_SESSION_ID = 'checkoutSessionId';
    public const REQUEST_ID = 'requestId';
    public const CHECKOUT_SESSION_URL = 'checkoutSessionUrl';
    public const EXPIRES_AT = 'expiresAt';
    public const CREATED_AT = 'createdAt';

    private ProspectId $prospectId;
    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private RequestId $requestId;
    private CheckoutSessionUrl $checkoutSessionUrl;
    private ExpiresAt $expiresAt;
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

    public function requestId(): RequestId
    {
        return $this->requestId;
    }

    public function checkoutSessionUrl(): CheckoutSessionUrl
    {
        return $this->checkoutSessionUrl;
    }

    public function expiresAt(): ExpiresAt
    {
        return $this->expiresAt;
    }

    public function createdAt(): CreatedAt
    {
        return $this->createdAt;
    }
}
