<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\Event;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\ImmutableEventTrait;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\DomainEvent;

final class DepositPaymentSessionCreationFailed implements DomainEvent
{
    use ImmutableEventTrait;

    private const EVENT_NAME = Context::NAME.'DepositPaymentSessionCreationFailed';

    public const RESERVATION_ID = 'reservationId';
    public const ERROR_COUNT = 'errorCount';
    public const CREATED_AT = 'createdAt';

    private ReservationId $reservationId;
    private ErrorCount $errorCount;
    private CreatedAt $createdAt;

    public static function eventName(): string
    {
        return self::EVENT_NAME;
    }

    public function reservationId(): ReservationId
    {
        return $this->reservationId;
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
