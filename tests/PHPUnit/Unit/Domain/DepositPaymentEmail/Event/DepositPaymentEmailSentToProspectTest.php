<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentEmail\Event;

use Allmyhomes\Domain\DepositPaymentEmail\Event\DepositPaymentEmailSentToProspect;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\RequestId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use PHPUnit\Framework\TestCase;

final class DepositPaymentEmailSentToProspectTest extends TestCase
{
    private DepositPaymentEmailSentToProspect $depositPaymentEmailSentToProspect;

    private ProspectId $prospectId;
    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private RequestId $requestId;
    private CheckoutSessionUrl $checkoutSessionUrl;
    private ExpiresAt $expiresAt;
    private CreatedAt $createdAt;

    public function setUp(): void
    {
        parent::setUp();

        $this->reservationId = ReservationId::fromString('1234-1234-1234');
        $this->prospectId = ProspectId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0');
        $this->checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1TpDn0YJ3sKoj7lbOq1DbWGrnGQMPT4Kdg837nN8pyFarROhHuvOp71BC');
        $this->requestId = RequestId::fromString('request id');
        $this->checkoutSessionUrl = CheckoutSessionUrl::fromString('https://www.example.com');
        $this->expiresAt = ExpiresAt::fromSeconds(1652873083);
        $this->createdAt = CreatedAt::fromDateTime(new \DateTimeImmutable());

        $this->depositPaymentEmailSentToProspect = DepositPaymentEmailSentToProspect::fromRecordData([
            DepositPaymentEmailSentToProspect::PROSPECT_ID => $this->prospectId,
            DepositPaymentEmailSentToProspect::RESERVATION_ID => $this->reservationId,
            DepositPaymentEmailSentToProspect::CHECKOUT_SESSION_ID => $this->checkoutSessionId,
            DepositPaymentEmailSentToProspect::REQUEST_ID => $this->requestId,
            DepositPaymentEmailSentToProspect::CHECKOUT_SESSION_URL => $this->checkoutSessionUrl,
            DepositPaymentEmailSentToProspect::EXPIRES_AT => $this->expiresAt,
            DepositPaymentEmailSentToProspect::CREATED_AT => $this->createdAt,
        ]);
    }

    public function testEventName(): void
    {
        self::assertEquals(
            'PaymentGateway.DepositPaymentEmailSentToProspect',
            $this->depositPaymentEmailSentToProspect->eventName(),
            'event name from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testProspectId(): void
    {
        self::assertEquals(
            $this->prospectId->toString(),
            $this->depositPaymentEmailSentToProspect->prospectId()->toString(),
            'prospect id from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testReservationId(): void
    {
        self::assertEquals(
            $this->reservationId->toString(),
            $this->depositPaymentEmailSentToProspect->reservationId()->toString(),
            'reservation id from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testCheckoutSessionId(): void
    {
        self::assertEquals(
            $this->checkoutSessionId->toString(),
            $this->depositPaymentEmailSentToProspect->checkoutSessionId()->toString(),
            'checkout session id from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testRequestId(): void
    {
        self::assertEquals(
            $this->requestId->toString(),
            $this->depositPaymentEmailSentToProspect->requestId()->toString(),
            'request id from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testCheckoutSessionUrl(): void
    {
        self::assertEquals(
            $this->checkoutSessionUrl->toString(),
            $this->depositPaymentEmailSentToProspect->checkoutSessionUrl()->toString(),
            'checkout session url from created deposit payment email sent to prospect event does not match expected string.',
        );
    }

    public function testExpiresAt(): void
    {
        self::assertEquals(
            $this->expiresAt->toSeconds(),
            $this->depositPaymentEmailSentToProspect->expiresAt()->toSeconds(),
            'expires at from created deposit payment email sent to prospect event does not match expected int.',
        );
    }

    public function testCreatedAt(): void
    {
        self::assertEquals(
            $this->createdAt->toString(),
            $this->depositPaymentEmailSentToProspect->createdAt()->toString(),
            'created at from created deposit payment email sent to prospect event does not match expected string.',
        );
    }
}
