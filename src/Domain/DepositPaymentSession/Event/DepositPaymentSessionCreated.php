<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\Event;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\Currency;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CustomerId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentIntentId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Domain\ValueObject\TotalUnitDeposit;
use Allmyhomes\Domain\ValueObject\UnitIdCollection;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\ImmutableEventTrait;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;

final class DepositPaymentSessionCreated implements DomainEvent
{
    use ImmutableEventTrait;

    private const EVENT_NAME = Context::NAME.'DepositPaymentSessionCreated';

    public const RESERVATION_ID = 'reservationId';
    public const AGENT_ID = 'agentId';
    public const LANGUAGE = 'language';
    public const PROJECT_ID = 'projectId';
    public const PROSPECT_ID = 'prospectId';
    public const TOTAL_UNIT_DEPOSIT = 'totalUnitDeposit';
    public const UNIT_IDS = 'unitIds';

    public const CHECKOUT_SESSION_ID = 'checkoutSessionId';
    public const CHECKOUT_SESSION_STATUS = 'checkoutSessionStatus';
    public const CHECKOUT_SESSION_URL = 'checkoutSessionUrl';
    public const CREATED_AT = 'createdAt';
    public const CURRENCY = 'currency';
    public const CUSTOMER_ID = 'customerId'; // id from Stripe created customer
    public const EXPIRES_AT = 'expiresAt';
    public const OCCURRED_AT = 'occurredAt';
    public const PAYMENT_INTENT_ID = 'paymentIntentId'; // id of Stripe payment
    public const PAYMENT_STATUS = 'paymentStatus';

    private ReservationId $reservationId;
    private AgentId $agentId;
    private Language $language;
    private ProjectId $projectId;
    private ProspectId $prospectId;
    private TotalUnitDeposit $totalUnitDeposit;
    private UnitIdCollection $unitIds;

    private CheckoutSessionId $checkoutSessionId;
    private ?CheckoutSessionStatus $checkoutSessionStatus;
    private ?CheckoutSessionUrl $checkoutSessionUrl;
    private CreatedAt $createdAt;
    private ?Currency $currency;
    private ?CustomerId $customerId;
    private ExpiresAt $expiresAt;
    private ?PaymentIntentId $paymentIntentId;
    private PaymentStatus $paymentStatus;

    public static function eventName(): string
    {
        return self::EVENT_NAME;
    }

    public function reservationId(): ReservationId
    {
        return $this->reservationId;
    }

    public function agentId(): AgentId
    {
        return $this->agentId;
    }

    public function language(): Language
    {
        return $this->language;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function prospectId(): ProspectId
    {
        return $this->prospectId;
    }

    public function totalUnitDeposit(): TotalUnitDeposit
    {
        return $this->totalUnitDeposit;
    }

    public function unitIds(): UnitIdCollection
    {
        return $this->unitIds;
    }

    public function checkoutSessionId(): CheckoutSessionId
    {
        return $this->checkoutSessionId;
    }

    public function checkoutSessionStatus(): ?CheckoutSessionStatus
    {
        return $this->checkoutSessionStatus;
    }

    public function checkoutSessionUrl(): ?CheckoutSessionUrl
    {
        return $this->checkoutSessionUrl;
    }

    public function createdAt(): CreatedAt
    {
        return $this->createdAt;
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
}
