<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentEmail\Aggregate;

use Allmyhomes\Domain\DepositPaymentEmail\Aggregate\DepositPaymentEmailState;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\RequestId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use PHPUnit\Framework\TestCase;

final class DepositPaymentEmailStateTest extends TestCase
{
    private DepositPaymentEmailState $depositPaymentEmailState;

    private ProspectId $prospectId;
    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private RequestId $requestId;
    private ErrorCount $errorCount;

    public function setUp(): void
    {
        parent::setUp();

        $this->prospectId = ProspectId::fromString('ca50819f-e5a4-40d3-a425-daba3e095407');
        $this->reservationId = ReservationId::fromString('1234-1234-1234');
        $this->checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1TpDn0YJ3sKoj7lbOq1DbWGrnGQMPT4Kdg837nN8pyFarROhHuvOp71BC');
        $this->requestId = RequestId::fromString('request id');
        $this->errorCount = ErrorCount::fromInt(0);

        $this->depositPaymentEmailState = DepositPaymentEmailState::fromRecordData([
            DepositPaymentEmailState::PROSPECT_ID => $this->prospectId,
            DepositPaymentEmailState::RESERVATION_ID => $this->reservationId,
            DepositPaymentEmailState::CHECKOUT_SESSION_ID => $this->checkoutSessionId,
            DepositPaymentEmailState::REQUEST_ID => $this->requestId,
            DepositPaymentEmailState::ERROR_COUNT => $this->errorCount,
        ]);
    }

    public function testProspectId(): void
    {
        self::assertEquals(
            $this->prospectId->toString(),
            $this->depositPaymentEmailState->prospectId()->toString(),
            'prospect id from created deposit payment email state does not match expected string.',
        );
    }

    public function testReservationId(): void
    {
        self::assertEquals(
            $this->reservationId->toString(),
            $this->depositPaymentEmailState->reservationId()->toString(),
            'reservation id from created deposit payment email state does not match expected string.',
        );
    }

    public function testCheckoutSessionId(): void
    {
        self::assertEquals(
            $this->checkoutSessionId->toString(),
            $this->depositPaymentEmailState->checkoutSessionId()->toString(),
            'checkout session id from created deposit payment email state does not match expected string.',
        );
    }

    public function testRequestId(): void
    {
        self::assertEquals(
            $this->requestId->toString(),
            $this->depositPaymentEmailState->requestId()?->toString(),
            'request id from created deposit payment email state does not match expected string.',
        );
    }

    public function testErrorCount(): void
    {
        self::assertEquals(
            $this->errorCount->toInt(),
            $this->depositPaymentEmailState->errorCount()->toInt(),
            'error count from created deposit payment email state does not match expected int.',
        );
    }
}
