<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\Aggregate;

use Allmyhomes\Domain\DepositPaymentSession\Aggregate\DepositPaymentSessionState;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ReservationId;
use PHPUnit\Framework\TestCase;

final class DepositPaymentSessionStateTest extends TestCase
{
    private DepositPaymentSessionState $depositPaymentSessionState;

    private ReservationId $id;
    private CheckoutSessionId $checkoutSessionId;
    private CheckoutSessionStatus $checkoutSessionStatus;
    private PaymentStatus $paymentStatus;
    private ErrorCount $errorCount;

    public function setUp(): void
    {
        parent::setUp();

        $this->id = ReservationId::fromString('1234-1234-1234');
        $this->checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1TpDn0YJ3sKoj7lbOq1DbWGrnGQMPT4Kdg837nN8pyFarROhHuvOp71BC');
        $this->checkoutSessionStatus = CheckoutSessionStatus::fromString(CheckoutSessionStatus::OPEN);
        $this->paymentStatus = PaymentStatus::fromString(PaymentStatus::UNPAID);
        $this->errorCount = ErrorCount::fromInt(0);

        $this->depositPaymentSessionState = DepositPaymentSessionState::fromRecordData([
            DepositPaymentSessionState::RESERVATION_ID => $this->id,
            DepositPaymentSessionState::CHECKOUT_SESSION_ID => $this->checkoutSessionId,
            DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => $this->checkoutSessionStatus,
            DepositPaymentSessionState::PAYMENT_STATUS => $this->paymentStatus,
            DepositPaymentSessionState::ERROR_COUNT => $this->errorCount,
        ]);
    }

    public function testId(): void
    {
        self::assertEquals(
            $this->id->toString(),
            $this->depositPaymentSessionState->reservationId()->toString(),
            'id from created deposit payment session state does not match expected string.',
        );
    }

    public function testCheckoutSessionId(): void
    {
        self::assertEquals(
            $this->checkoutSessionId->toString(),
            $this->depositPaymentSessionState->checkoutSessionId()?->toString(),
            'checkout session id from created deposit payment session state does not match expected string.',
        );
    }

    public function testCheckoutSessionStatus(): void
    {
        self::assertEquals(
            $this->checkoutSessionStatus->toString(),
            $this->depositPaymentSessionState->checkoutSessionStatus()?->toString(),
            'checkout session status from created deposit payment session state does not match expected string.',
        );
    }

    public function testPaymentStatus(): void
    {
        self::assertEquals(
            $this->paymentStatus->toString(),
            $this->depositPaymentSessionState->paymentStatus()?->toString(),
            'payment status from created deposit payment session state does not match expected string.',
        );
    }

    public function testErrorCount(): void
    {
        self::assertEquals(
            $this->errorCount->toInt(),
            $this->depositPaymentSessionState->errorCount()->toInt(),
            'error count from created deposit payment session state does not match expected int.',
        );
    }
}
