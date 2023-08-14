<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\Event;

use Allmyhomes\Domain\DepositPaymentSession\Event\DepositPaymentSessionCreationFailed;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ReservationId;
use PHPUnit\Framework\TestCase;

final class DepositPaymentSessionCreationFailedTest extends TestCase
{
    private DepositPaymentSessionCreationFailed $checkoutSessionCreationFailed;

    private ReservationId $reservationId;
    private ErrorCount $errorCount;
    private CreatedAt $createdAt;

    public function setUp(): void
    {
        parent::setUp();

        $this->reservationId = ReservationId::fromString('1234-1234-1234');
        $this->errorCount = ErrorCount::fromInt(1);
        $this->createdAt = CreatedAt::fromString('2016-06-16T16:00:00+00:00');

        $this->checkoutSessionCreationFailed = DepositPaymentSessionCreationFailed::fromRecordData([
            DepositPaymentSessionCreationFailed::RESERVATION_ID => $this->reservationId,
            DepositPaymentSessionCreationFailed::ERROR_COUNT => $this->errorCount,
            DepositPaymentSessionCreationFailed::CREATED_AT => $this->createdAt,
        ]);
    }

    public function testEventName(): void
    {
        self::assertEquals(
             'PaymentGateway.DepositPaymentSessionCreationFailed',
            $this->checkoutSessionCreationFailed->eventName(),
            'event name from created deposit payment session failed event does not match expected event name.',
        );
    }

    public function testReservationId(): void
    {
        self::assertEquals(
            $this->reservationId->toString(),
            $this->checkoutSessionCreationFailed->reservationId()->toString(),
            'reservation id from deposit payment session failed event does not match expected reservation id.',
        );
    }

    public function testErrorCount(): void
    {
        self::assertEquals(
            $this->errorCount->toInt(),
            $this->checkoutSessionCreationFailed->errorCount()->toInt(),
            'error count from deposit payment session failed event does not match expected error count.',
        );
    }

    public function testCreatedAt(): void
    {
        self::assertEquals(
            $this->createdAt->toString(),
            $this->checkoutSessionCreationFailed->createdAt()->toString(),
            'created at from deposit payment session failed event does not match expected created at.',
        );
    }
}
