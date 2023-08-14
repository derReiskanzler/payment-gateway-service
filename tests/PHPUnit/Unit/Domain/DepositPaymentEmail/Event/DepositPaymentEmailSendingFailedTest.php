<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentEmail\Event;

use Allmyhomes\Domain\DepositPaymentEmail\Event\DepositPaymentEmailSendingFailed;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use PHPUnit\Framework\TestCase;

final class DepositPaymentEmailSendingFailedTest extends TestCase
{
    private DepositPaymentEmailSendingFailed $depositPaymentEmailSendingFailed;

    private ProspectId $prospectId;
    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private CheckoutSessionUrl $checkoutSessionUrl;
    private ExpiresAt $expiresAt;
    private ErrorCount $errorCount;
    private CreatedAt $createdAt;

    public function setUp(): void
    {
        parent::setUp();

        $this->reservationId = ReservationId::fromString('1234-1234-1234');
        $this->prospectId = ProspectId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0');
        $this->checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1TpDn0YJ3sKoj7lbOq1DbWGrnGQMPT4Kdg837nN8pyFarROhHuvOp71BC');
        $this->checkoutSessionUrl = CheckoutSessionUrl::fromString('https://www.example.com');
        $this->expiresAt = ExpiresAt::fromSeconds(1652873083);
        $this->errorCount = ErrorCount::fromInt(1);
        $this->createdAt = CreatedAt::fromDateTime(new \DateTimeImmutable());

        $this->depositPaymentEmailSendingFailed = DepositPaymentEmailSendingFailed::fromRecordData([
            DepositPaymentEmailSendingFailed::PROSPECT_ID => $this->prospectId,
            DepositPaymentEmailSendingFailed::RESERVATION_ID => $this->reservationId,
            DepositPaymentEmailSendingFailed::CHECKOUT_SESSION_ID => $this->checkoutSessionId,
            DepositPaymentEmailSendingFailed::CHECKOUT_SESSION_URL => $this->checkoutSessionUrl,
            DepositPaymentEmailSendingFailed::EXPIRES_AT => $this->expiresAt,
            DepositPaymentEmailSendingFailed::ERROR_COUNT => $this->errorCount,
            DepositPaymentEmailSendingFailed::CREATED_AT => $this->createdAt,
        ]);
    }

    public function testEventName(): void
    {
        self::assertEquals(
            'PaymentGateway.DepositPaymentEmailSendingFailed',
            $this->depositPaymentEmailSendingFailed->eventName(),
            'event name from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testProspectId(): void
    {
        self::assertEquals(
            $this->prospectId->toString(),
            $this->depositPaymentEmailSendingFailed->prospectId()->toString(),
            'prospect id from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testReservationId(): void
    {
        self::assertEquals(
            $this->reservationId->toString(),
            $this->depositPaymentEmailSendingFailed->reservationId()->toString(),
            'reservation id from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testCheckoutSessionId(): void
    {
        self::assertEquals(
            $this->checkoutSessionId->toString(),
            $this->depositPaymentEmailSendingFailed->checkoutSessionId()->toString(),
            'checkout session id from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testCheckoutSessionUrl(): void
    {
        self::assertEquals(
            $this->checkoutSessionUrl->toString(),
            $this->depositPaymentEmailSendingFailed->checkoutSessionUrl()->toString(),
            'checkout session url from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testExpiresAt(): void
    {
        self::assertEquals(
            $this->expiresAt->toSeconds(),
            $this->depositPaymentEmailSendingFailed->expiresAt()->toSeconds(),
            'expires at from created deposit payment email sent to prospect event does not match expected int.',
        );
    }

    public function testErrorCount(): void
    {
        self::assertEquals(
            $this->errorCount->toInt(),
            $this->depositPaymentEmailSendingFailed->errorCount()->toInt(),
            'error count from created deposit payment email sent to prospect event does not match expected int.',
        );
    }

    public function testCreatedAt(): void
    {
        self::assertEquals(
            $this->createdAt->toString(),
            $this->depositPaymentEmailSendingFailed->createdAt()->toString(),
            'created at from created deposit payment email sent to prospect event does not match expected string.',
        );
    }
}
