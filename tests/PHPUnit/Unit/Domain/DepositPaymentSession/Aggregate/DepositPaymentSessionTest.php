<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\Aggregate;

use Allmyhomes\Domain\DepositPaymentSession\Aggregate\DepositPaymentSession;
use Allmyhomes\Domain\DepositPaymentSession\Aggregate\DepositPaymentSessionState;
use Allmyhomes\Domain\DepositPaymentSession\Exception\CouldNotCreateCheckoutSessionException;
use Allmyhomes\Domain\DepositPaymentSession\Exception\DepositDisabledException;
use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSession;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class DepositPaymentSessionTest extends TestCase
{
    private ReservationId $id;
    private AgentId $agentId;
    private Language $language;
    private ProjectId $projectId;
    private ProspectId $prospectId;
    private UnitCollection $unitCollection;
    private ErrorCount $errorCount;
    private DepositTransferDeadline $depositTransferDeadline;

    /**
     * @var StripeServiceInterface&MockObject
     */
    private StripeServiceInterface $stripeService;

    public function setUp(): void
    {
        parent::setUp();

        $this->id = ReservationId::fromString('1234-1234-1234');
        $this->language = Language::fromString('de');
        $this->agentId = AgentId::fromString('b72a7cdc-0d36-411e-b897-c9dd0a79c318');
        $this->projectId = ProjectId::fromInt(80262);
        $this->prospectId = ProspectId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0');
        $this->unitCollection = UnitCollection::fromArray([
            0 => [
                'id' => 1,
                'name' => 'WE 1',
                'deposit' => 3000.00,
            ],
        ]);
        $this->errorCount = ErrorCount::fromInt(0);
        $this->depositTransferDeadline = DepositTransferDeadline::fromSeconds(1653004800);

        $this->stripeService = $this->createMock(StripeServiceInterface::class);
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideDepositPaymentSessionData
     */
    public function testCreateNewDepositPaymentSession(array $checkoutSessionDataArray): void
    {
        $this->stripeService
            ->expects(self::once())
            ->method('createCheckoutSession')
            ->willReturn(CheckoutSession::fromArray($checkoutSessionDataArray));

        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID],
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_STATUS],
                DepositPaymentSessionState::PAYMENT_STATUS => $checkoutSessionDataArray[CheckoutSession::PAYMENT_STATUS],
                DepositPaymentSessionState::ERROR_COUNT => ErrorCount::fromInt(0)->toInt(),
            ],
            $depositPaymentSession->state()->toArray(),
            'created deposit payment session aggregate array does not match deposit payment session created array.'
        );
    }

    public function testCreateDepositPaymentSessionWithNoDeposit(): void
    {
        $this->expectException(DepositDisabledException::class);

        $this->stripeService
            ->expects(self::never())
            ->method('createCheckoutSession');

        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            UnitCollection::fromArray([
                0 => [
                    'id' => 1,
                    'deposit' => 0,
                    'name' => 'WE 1',
                ],
            ]),
            $this->stripeService,
        );

        self::assertEquals(
            null,
            $depositPaymentSession,
            'creation of deposit payment session aggregate does not match expected null.'
        );
    }

    public function testCreateDepositPaymentSessionWithEmptyCheckoutSession(): void
    {
        $this->stripeService
            ->expects(self::once())
            ->method('createCheckoutSession')
            ->willReturn(null);

        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => null,
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => null,
                DepositPaymentSessionState::PAYMENT_STATUS => null,
                DepositPaymentSessionState::ERROR_COUNT => 1,
            ],
            $depositPaymentSession->state()->toArray(),
            'creation of deposit payment session aggregate does not match expected null.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideDepositPaymentSessionData
     */
    public function testRetryDepositPaymentSessionCreation(array $checkoutSessionDataArray): void
    {
        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => null,
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => null,
                DepositPaymentSessionState::PAYMENT_STATUS => null,
                DepositPaymentSessionState::ERROR_COUNT => 1,
            ],
            $depositPaymentSession->state()->toArray(),
            'retry creation of deposit payment session aggregate does not match expected array.'
        );

        $this->stripeService
            ->expects(self::once())
            ->method('createCheckoutSession')
            ->willReturn(CheckoutSession::fromArray($checkoutSessionDataArray));

        $depositPaymentSession->retryDepositPaymentSessionCreation(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
            $this->errorCount,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID],
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_STATUS],
                DepositPaymentSessionState::PAYMENT_STATUS => $checkoutSessionDataArray[CheckoutSession::PAYMENT_STATUS],
                DepositPaymentSessionState::ERROR_COUNT => 0,
            ],
            $depositPaymentSession->state()->toArray(),
            'retry creation of deposit payment session aggregate does not match expected array.'
        );
    }

    public function testRetryDepositPaymentSessionCreationWithExceededErrorCount(): void
    {
        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => null,
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => null,
                DepositPaymentSessionState::PAYMENT_STATUS => null,
                DepositPaymentSessionState::ERROR_COUNT => 1,
            ],
            $depositPaymentSession->state()->toArray(),
            'retry creation of deposit payment session aggregate does not match expected array.'
        );

        $this->expectException(CouldNotCreateCheckoutSessionException::class);

        $this->stripeService
            ->expects(self::never())
            ->method('createCheckoutSession');

        $depositPaymentSession->retryDepositPaymentSessionCreation(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
            ErrorCount::fromInt(6),
        );
    }

    public function testRetryDepositPaymentSessionCreationWithEmptyCheckoutSession(): void
    {
        $this->stripeService
            ->expects(self::exactly(2))
            ->method('createCheckoutSession')
            ->willReturn(null);

        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => null,
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => null,
                DepositPaymentSessionState::PAYMENT_STATUS => null,
                DepositPaymentSessionState::ERROR_COUNT => 1,
            ],
            $depositPaymentSession->state()->toArray(),
            'creation of deposit payment session aggregate does not match expected array.'
        );

        $depositPaymentSession->retryDepositPaymentSessionCreation(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
            ErrorCount::fromInt($depositPaymentSession->state()->toArray()['errorCount']),
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => null,
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => null,
                DepositPaymentSessionState::PAYMENT_STATUS => null,
                DepositPaymentSessionState::ERROR_COUNT => 2,
            ],
            $depositPaymentSession->state()->toArray(),
            'retry creation of deposit payment session aggregate does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideDepositPaymentSessionData
     */
    public function testCompleteDepositPaymentSession(
        array $checkoutSessionDataArray,
    ): void {
        $this->stripeService
            ->expects(self::once())
            ->method('createCheckoutSession')
            ->willReturn(CheckoutSession::fromArray($checkoutSessionDataArray));

        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID],
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_STATUS],
                DepositPaymentSessionState::PAYMENT_STATUS => $checkoutSessionDataArray[CheckoutSession::PAYMENT_STATUS],
                DepositPaymentSessionState::ERROR_COUNT => ErrorCount::fromInt(0)->toInt(),
            ],
            $depositPaymentSession->state()->toArray(),
            'deposit payment session aggregate state after creating new session does not match expected array.'
        );

        $checkoutSessionId = CheckoutSessionId::fromString($checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID]);
        $checkoutSessionStatus = CheckoutSessionStatus::fromString(CheckoutSessionStatus::COMPLETE);
        $paymentStatus = PaymentStatus::fromString(PaymentStatus::PAID);

        $depositPaymentSession->completeDepositPaymentSession(
            $checkoutSessionId,
            $checkoutSessionStatus,
            $paymentStatus,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => $checkoutSessionId->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => CheckoutSessionStatus::COMPLETE,
                DepositPaymentSessionState::PAYMENT_STATUS => PaymentStatus::PAID,
                DepositPaymentSessionState::ERROR_COUNT => 0,
            ],
            $depositPaymentSession->state()->toArray(),
            'deposit payment session aggregate state after completing session does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideDepositPaymentSessionData
     */
    public function testCompleteDepositPaymentSessionWithOpenStatus(
        array $checkoutSessionDataArray,
    ): void {
        $this->stripeService
            ->expects(self::once())
            ->method('createCheckoutSession')
            ->willReturn(CheckoutSession::fromArray($checkoutSessionDataArray));

        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID],
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_STATUS],
                DepositPaymentSessionState::PAYMENT_STATUS => $checkoutSessionDataArray[CheckoutSession::PAYMENT_STATUS],
                DepositPaymentSessionState::ERROR_COUNT => ErrorCount::fromInt(0)->toInt(),
            ],
            $depositPaymentSession->state()->toArray(),
            'deposit payment session aggregate state after creating new session does not match expected array.'
        );

        $checkoutSessionId = CheckoutSessionId::fromString($checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID]);
        $checkoutSessionStatus = CheckoutSessionStatus::fromString(CheckoutSessionStatus::OPEN);
        $paymentStatus = PaymentStatus::fromString(PaymentStatus::PAID);

        $depositPaymentSession->completeDepositPaymentSession(
            $checkoutSessionId,
            $checkoutSessionStatus,
            $paymentStatus,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => $checkoutSessionId->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => CheckoutSessionStatus::OPEN,
                DepositPaymentSessionState::PAYMENT_STATUS => PaymentStatus::UNPAID,
                DepositPaymentSessionState::ERROR_COUNT => 0,
            ],
            $depositPaymentSession->state()->toArray(),
            'deposit payment session aggregate state after completing session does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideDepositPaymentSessionData
     */
    public function testCompleteDepositPaymentSessionWithExpiredStatus(
        array $checkoutSessionDataArray,
    ): void {
        $this->stripeService
            ->expects(self::once())
            ->method('createCheckoutSession')
            ->willReturn(CheckoutSession::fromArray($checkoutSessionDataArray));

        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID],
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_STATUS],
                DepositPaymentSessionState::PAYMENT_STATUS => $checkoutSessionDataArray[CheckoutSession::PAYMENT_STATUS],
                DepositPaymentSessionState::ERROR_COUNT => ErrorCount::fromInt(0)->toInt(),
            ],
            $depositPaymentSession->state()->toArray(),
            'deposit payment session aggregate state after creating new session does not match expected array.'
        );

        $checkoutSessionId = CheckoutSessionId::fromString($checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID]);
        $checkoutSessionStatus = CheckoutSessionStatus::fromString(CheckoutSessionStatus::EXPIRED);
        $paymentStatus = PaymentStatus::fromString(PaymentStatus::PAID);

        $depositPaymentSession->completeDepositPaymentSession(
            $checkoutSessionId,
            $checkoutSessionStatus,
            $paymentStatus,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => $checkoutSessionId->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => CheckoutSessionStatus::OPEN,
                DepositPaymentSessionState::PAYMENT_STATUS => PaymentStatus::UNPAID,
                DepositPaymentSessionState::ERROR_COUNT => 0,
            ],
            $depositPaymentSession->state()->toArray(),
            'deposit payment session aggregate state after completing session does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideDepositPaymentSessionData
     */
    public function testCompleteDepositPaymentSessionWithWrongStatus(
        array $checkoutSessionDataArray,
    ): void {
        $this->stripeService
            ->expects(self::once())
            ->method('createCheckoutSession')
            ->willReturn(CheckoutSession::fromArray($checkoutSessionDataArray));

        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID],
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_STATUS],
                DepositPaymentSessionState::PAYMENT_STATUS => $checkoutSessionDataArray[CheckoutSession::PAYMENT_STATUS],
                DepositPaymentSessionState::ERROR_COUNT => ErrorCount::fromInt(0)->toInt(),
            ],
            $depositPaymentSession->state()->toArray(),
            'deposit payment session aggregate state after creating new session does not match expected array.'
        );

        $this->expectException(InvalidArgumentException::class);

        $checkoutSessionId = CheckoutSessionId::fromString($checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID]);
        $checkoutSessionStatus = CheckoutSessionStatus::fromString('invalid status');
        $paymentStatus = PaymentStatus::fromString(PaymentStatus::PAID);

        $depositPaymentSession->completeDepositPaymentSession(
            $checkoutSessionId,
            $checkoutSessionStatus,
            $paymentStatus,
        );

        self::assertEquals(
            [
                DepositPaymentSessionState::RESERVATION_ID => $this->id->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_ID => $checkoutSessionId->toString(),
                DepositPaymentSessionState::CHECKOUT_SESSION_STATUS => CheckoutSessionStatus::OPEN,
                DepositPaymentSessionState::PAYMENT_STATUS => PaymentStatus::UNPAID,
                DepositPaymentSessionState::ERROR_COUNT => 0,
            ],
            $depositPaymentSession->state()->toArray(),
            'deposit payment session aggregate state after completing session does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideDepositPaymentSessionData
     */
    public function testAggregateId(array $checkoutSessionDataArray): void
    {
        $this->stripeService
            ->expects(self::once())
            ->method('createCheckoutSession')
            ->willReturn(CheckoutSession::fromArray($checkoutSessionDataArray));

        $depositPaymentSession = DepositPaymentSession::createNewDepositPaymentSession(
            $this->id,
            $this->agentId,
            $this->projectId,
            $this->prospectId,
            $this->language,
            $this->depositTransferDeadline,
            $this->unitCollection,
            $this->stripeService,
        );

        self::assertInstanceOf(
            AggregateId::class,
            $depositPaymentSession->aggregateId(),
            'aggregate id of deposit payment session does not match expected class: AggregateId.'
        );
        self::assertEquals(
            $this->id->toString(),
            $depositPaymentSession->aggregateId()->toString(),
            'aggregate id of deposit payment session does not match expected string.'
        );
    }

    public function provideDepositPaymentSessionData(): Generator
    {
        yield 'CheckoutSession data' => [
            'checkout session array data' => [
                CheckoutSession::CHECKOUT_SESSION_ID => 'cs_test_a1TpDn0YJ3sKoj7lbOq1DbWGrnGQMPT4Kdg837nN8pyFarROhHuvOp71BC',
                CheckoutSession::CHECKOUT_SESSION_STATUS => CheckoutSessionStatus::OPEN,
                CheckoutSession::CHECKOUT_SESSION_URL => 'https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl',
                CheckoutSession::CURRENCY => 'eur',
                CheckoutSession::CUSTOMER_ID => 'customer id',
                CheckoutSession::EXPIRES_AT => 1653004800,
                CheckoutSession::PAYMENT_INTENT_ID => 'pi_3KSOXmJHRV8spf0Q1Vaclh9l',
                CheckoutSession::PAYMENT_STATUS => PaymentStatus::UNPAID,
            ],
        ];
    }
}
