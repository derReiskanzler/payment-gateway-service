<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\CompleteDepositPaymentSession;

use Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Command\CompleteDepositPaymentSession;
use Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Command\CompleteDepositPaymentSessionHandler;
use Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Exception\DepositPaymentSessionNotFoundException;
use Allmyhomes\Application\UseCase\PopulateReservation\Document\Reservation;
use Allmyhomes\Application\UseCase\PopulateUnit\Document\Unit as UnitReadModel;
use Allmyhomes\Domain\DepositPaymentSession\Aggregate\DepositPaymentSession;
use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Allmyhomes\Domain\DepositPaymentSession\Repository\DepositPaymentSessionRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSession;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\Reservation\ValueObject\Unit;
use Allmyhomes\Domain\Reservation\ValueObject\UnitCollection as ReservationUnitCollection;
use Allmyhomes\Domain\Unit\ValueObject\UnitCollection as UnitReadModelCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Domain\ValueObject\UnitDeposit;
use Allmyhomes\Domain\ValueObject\UnitId;
use Allmyhomes\Domain\ValueObject\UnitName;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Generator;
use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CompleteDepositPaymentSessionHandlerTest extends TestCase
{
    private CompleteDepositPaymentSessionHandler $handler;

    /**
     * @var DepositPaymentSessionRepositoryInterface&MockObject
     */
    private DepositPaymentSessionRepositoryInterface $depositPaymentSessionRepository;

    /**
     * @var StripeServiceInterface&MockObject
     */
    private StripeServiceInterface $stripeApiClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->depositPaymentSessionRepository = $this->createMock(DepositPaymentSessionRepositoryInterface::class);
        $this->stripeApiClient = $this->createMock(StripeServiceInterface::class);

        $this->handler = new CompleteDepositPaymentSessionHandler($this->depositPaymentSessionRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * @dataProvider provideCompleteDepositPaymentSessionData
     */
    public function testHandle(
        CompleteDepositPaymentSession $command,
        Reservation $reservation,
        UnitReadModelCollection $unitReadModelCollection,
        CheckoutSession $checkoutSession
    ): void {
        $this->depositPaymentSessionRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn(
                DepositPaymentSession::createNewDepositPaymentSession(
                    $reservation->id(),
                    $reservation->agentId(),
                    $reservation->projectId(),
                    $reservation->prospectId(),
                    $reservation->language(),
                    $reservation->depositTransferDeadline(),
                    UnitCollection::fromReadModelUnitCollections($unitReadModelCollection, $reservation->units()),
                    $this->stripeApiClient,
                )
            );

        $this->depositPaymentSessionRepository
            ->expects(self::once())
            ->method('save');

        $this->handler->handle($command);
    }

    /**
     * @dataProvider provideCompleteDepositPaymentSessionData
     */
    public function testDepositPaymentSessionNotFoundException(CompleteDepositPaymentSession $command): void
    {
        $this->expectException(DepositPaymentSessionNotFoundException::class);

        $this->depositPaymentSessionRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn(null);

        $this->depositPaymentSessionRepository
            ->expects(self::never())
            ->method('save');

        $this->handler->handle($command);
    }

    public function provideCompleteDepositPaymentSessionData(): Generator
    {
        $checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1TpDn0YJ3sKoj7lbOq1DbWGrnGQMPT4Kdg837nN8pyFarROhHuvOp71BC');
        $checkoutSessionStatus = CheckoutSessionStatus::fromString(CheckoutSessionStatus::OPEN);
        $paymentStatus = PaymentStatus::fromString(PaymentStatus::UNPAID);
        $reservationId = ReservationId::fromString('1234-1234-1234');
        $reservationUnitCollection = ReservationUnitCollection::fromUnits(
            new Unit(
                UnitId::fromInt(1),
                UnitDeposit::fromFloat(3000.0)
            )
        );
        $unitReadModelCollection = UnitReadModelCollection::fromUnits(
            new UnitReadModel(
                UnitId::fromInt(1),
                UnitName::fromString('WE 1')
            ),
        );

        yield 'CompleteDepositPaymentSessionCommandHandler data' => [
            new CompleteDepositPaymentSession(
                CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0'),
                $reservationId,
                $checkoutSessionId,
                $checkoutSessionStatus,
                $paymentStatus,
            ),
            new Reservation(
                $reservationId,
                AgentId::fromString('b72a7cdc-0d36-411e-b897-c9dd0a79c318'),
                DepositTransferDeadline::fromSeconds(1653004800),
                Language::fromString('de'),
                ProjectId::fromInt(80262),
                ProspectId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0'),
                $reservationUnitCollection->totalUnitDeposit(),
                $reservationUnitCollection,
            ),
            $unitReadModelCollection,
            CheckoutSession::fromArray([
                CheckoutSession::CHECKOUT_SESSION_ID => $checkoutSessionId->toString(),
                CheckoutSession::CHECKOUT_SESSION_STATUS => $checkoutSessionStatus->toString(),
                CheckoutSession::CHECKOUT_SESSION_URL => 'https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl',
                CheckoutSession::CURRENCY => 'eur',
                CheckoutSession::CUSTOMER_ID => 'customer id',
                CheckoutSession::EXPIRES_AT => 1653004800,
                CheckoutSession::PAYMENT_INTENT_ID => 'pi_3KSOXmJHRV8spf0Q1Vaclh9l',
                CheckoutSession::PAYMENT_STATUS => $paymentStatus->toString(),
            ]),
        ];
    }
}
