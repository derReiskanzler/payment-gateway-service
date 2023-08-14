<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\CompleteDepositPaymentSession;

use Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Command\CompleteDepositPaymentSession;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use PHPUnit\Framework\TestCase;

final class CompleteDepositPaymentSessionTest extends TestCase
{
    private CompleteDepositPaymentSession $completeDepositPaymentSession;
    private CommandId $commandId;
    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private CheckoutSessionStatus $checkoutSessionStatus;
    private PaymentStatus $paymentStatus;

    public function setUp(): void
    {
        parent::setUp();

        $this->commandId = CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0');
        $this->reservationId = ReservationId::fromString('1234-1234-1234');
        $this->checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2');
        $this->checkoutSessionStatus = CheckoutSessionStatus::fromString(CheckoutSessionStatus::COMPLETE);
        $this->paymentStatus = PaymentStatus::fromString(PaymentStatus::PAID);

        $this->completeDepositPaymentSession = new CompleteDepositPaymentSession(
            $this->commandId,
            $this->reservationId,
            $this->checkoutSessionId,
            $this->checkoutSessionStatus,
            $this->paymentStatus,
            [],
        );
    }

    public function testCommandName(): void
    {
        self::assertEquals(
            'PaymentGateway.CompleteDepositPaymentSession',
            $this->completeDepositPaymentSession->commandName(),
            'command name of complete deposit payment session does not match expected command name.'
        );
    }

    public function testUuid(): void
    {
        self::assertEquals(
            $this->commandId,
            $this->completeDepositPaymentSession->uuid(),
            'id of complete deposit payment session command does not match expected command id.'
        );
    }

    public function testMetadata(): void
    {
        self::assertEquals(
            [],
            $this->completeDepositPaymentSession->metadata(),
            'metadata of complete deposit payment session command does not match expected metadata.'
        );
    }

    public function testReservationId(): void
    {
        self::assertEquals(
            $this->reservationId,
            $this->completeDepositPaymentSession->reservationId(),
            'reservation id of complete deposit payment session command does not match expected reservation id.'
        );
    }

    public function testCheckoutSessionId(): void
    {
        self::assertEquals(
            $this->checkoutSessionId,
            $this->completeDepositPaymentSession->checkoutSessionId(),
            'checkout session id of complete deposit payment session command does not match expected checkout session id.'
        );
    }

    public function testCheckoutSessionStatus(): void
    {
        self::assertEquals(
            $this->checkoutSessionStatus,
            $this->completeDepositPaymentSession->checkoutSessionStatus(),
            'checkout session status of complete deposit payment session command does not match expected checkout session status.'
        );
    }

    public function testPaymentStatus(): void
    {
        self::assertEquals(
            $this->paymentStatus,
            $this->completeDepositPaymentSession->paymentStatus(),
            'payment status of complete deposit payment session command does not match expected payment status.'
        );
    }
}
