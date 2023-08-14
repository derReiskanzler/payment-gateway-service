<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\Event;

use Allmyhomes\Domain\DepositPaymentSession\Event\DepositPaymentSessionCompleted;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\ReservationId;
use PHPUnit\Framework\TestCase;

final class DepositPaymentSessionCompletedTest extends TestCase
{
    private DepositPaymentSessionCompleted $depositPaymentSessionCreated;

    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private CheckoutSessionStatus $checkoutSessionStatus;
    private PaymentStatus $paymentStatus;
    private CreatedAt $createdAt;

    public function setUp(): void
    {
        parent::setUp();

        $this->reservationId = ReservationId::fromString('1234-1234-1234');

        $this->checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1TpDn0YJ3sKoj7lbOq1DbWGrnGQMPT4Kdg837nN8pyFarROhHuvOp71BC');
        $this->checkoutSessionStatus = CheckoutSessionStatus::fromString(CheckoutSessionStatus::OPEN);
        $this->paymentStatus = PaymentStatus::fromString(PaymentStatus::UNPAID);
        $this->createdAt = CreatedAt::fromDateTime(new \DateTimeImmutable());

        $this->depositPaymentSessionCreated = DepositPaymentSessionCompleted::fromRecordData([
            DepositPaymentSessionCompleted::RESERVATION_ID => $this->reservationId,
            DepositPaymentSessionCompleted::CHECKOUT_SESSION_ID => $this->checkoutSessionId,
            DepositPaymentSessionCompleted::STATUS => $this->checkoutSessionStatus,
            DepositPaymentSessionCompleted::PAYMENT_STATUS => $this->paymentStatus,
            DepositPaymentSessionCompleted::CREATED_AT => $this->createdAt,
        ]);
    }

    public function testEventName(): void
    {
        self::assertEquals(
            'PaymentGateway.DepositPaymentSessionCompleted',
            $this->depositPaymentSessionCreated->eventName(),
            'event name from created deposit payment session created event does not match expected event name.',
        );
    }

    public function testReservationId(): void
    {
        self::assertEquals(
            $this->reservationId->toString(),
            $this->depositPaymentSessionCreated->reservationId()->toString(),
            'reservation id from created deposit payment session created event does not match expected reservation id.',
        );
    }

    public function testCheckoutSessionId(): void
    {
        self::assertEquals(
            $this->checkoutSessionId->toString(),
            $this->depositPaymentSessionCreated->checkoutSessionId()->toString(),
            'checkoutSessionId from created deposit payment session created event does not match expected checkoutSessionId.',
        );
    }

    public function testCheckoutSessionStatus(): void
    {
        self::assertEquals(
            $this->checkoutSessionStatus->toString(),
            $this->depositPaymentSessionCreated->status()->toString(),
            'checkoutSessionStatus from created deposit payment session created event does not match expected checkoutSessionStatus.',
        );
    }

    public function testPaymentStatus(): void
    {
        self::assertEquals(
            $this->paymentStatus->toString(),
            $this->depositPaymentSessionCreated->paymentStatus()->toString(),
            'paymentStatus from created deposit payment session created event does not match expected paymentStatus.',
        );
    }

    public function testCreatedAt(): void
    {
        self::assertEquals(
            $this->createdAt->toString(),
            $this->depositPaymentSessionCreated->createdAt()->toString(),
            'createdAt from created deposit payment session created event does not match expected createdAt.',
        );
    }
}
