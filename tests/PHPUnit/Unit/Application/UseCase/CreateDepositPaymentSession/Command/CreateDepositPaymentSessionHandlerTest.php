<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\CreateDepositPaymentSession\Command;

use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command\CreateDepositPaymentSession;
use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command\CreateDepositPaymentSessionHandler;
use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Exception\ReservationNotFoundException;
use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Exception\UnitsNotFoundException;
use Allmyhomes\Application\UseCase\PopulateReservation\Document\Reservation;
use Allmyhomes\Application\UseCase\PopulateReservation\Repository\ReservationRepositoryInterface;
use Allmyhomes\Application\UseCase\PopulateUnit\Document\Unit as UnitReadModel;
use Allmyhomes\Application\UseCase\PopulateUnit\Repository\UnitRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\Aggregate\DepositPaymentSession;
use Allmyhomes\Domain\DepositPaymentSession\Exception\DepositDisabledException;
use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Allmyhomes\Domain\DepositPaymentSession\Repository\DepositPaymentSessionRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSession;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\Reservation\ValueObject\Unit;
use Allmyhomes\Domain\Reservation\ValueObject\UnitCollection;
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
use Psr\Log\LoggerInterface;

final class CreateDepositPaymentSessionHandlerTest extends TestCase
{
    private CreateDepositPaymentSessionHandler $handler;

    /**
     * @var DepositPaymentSessionRepositoryInterface&MockObject
     */
    private DepositPaymentSessionRepositoryInterface $depositPaymentSessionRepository;

    /**
     * @var StripeServiceInterface&MockObject
     */
    private StripeServiceInterface $stripeApiClient;

    /**
     * @var ReservationRepositoryInterface&MockObject
     */
    private ReservationRepositoryInterface $reservationRepository;

    /**
     * @var UnitRepositoryInterface&MockObject
     */
    private UnitRepositoryInterface $unitRepository;

    /**
     * @var LoggerInterface&MockObject
     */
    private LoggerInterface $logger;

    public function setUp(): void
    {
        parent::setUp();

        $this->depositPaymentSessionRepository = $this->createMock(DepositPaymentSessionRepositoryInterface::class);
        $this->stripeApiClient = $this->createMock(StripeServiceInterface::class);
        $this->reservationRepository = $this->createMock(ReservationRepositoryInterface::class);
        $this->unitRepository = $this->createMock(UnitRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->handler = new CreateDepositPaymentSessionHandler(
            $this->depositPaymentSessionRepository,
            $this->stripeApiClient,
            $this->reservationRepository,
            $this->unitRepository,
            $this->logger,
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * @dataProvider provideCreateDepositPaymentSessionData
     */
    public function testHandle(
        CreateDepositPaymentSession $command,
        Reservation $reservation,
        CheckoutSession $checkoutSession
    ): void {
        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn($reservation);

        $this->unitRepository
            ->expects(self::once())
            ->method('getByIds')
            ->willReturn(UnitReadModelCollection::fromUnits(
                new UnitReadModel(
                    UnitId::fromInt(1),
                    UnitName::fromString('WE 1')
                )
            ));

        $this->stripeApiClient
            ->expects(self::once())
            ->method('createCheckoutSession')
            ->willReturn($checkoutSession);

        $this->depositPaymentSessionRepository
            ->expects(self::once())
            ->method('save');

        $this->handler->handle($command);
    }

    /**
     * @dataProvider provideCreateDepositPaymentSessionData
     */
    public function testReservationNotFoundException(CreateDepositPaymentSession $command): void
    {
        $this->expectException(ReservationNotFoundException::class);

        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn(null);

        $this->unitRepository
            ->expects(self::never())
            ->method('getByIds');

        $this->stripeApiClient
            ->expects(self::never())
            ->method('createCheckoutSession');

        $this->depositPaymentSessionRepository
            ->expects(self::never())
            ->method('save');

        $this->handler->handle($command);
    }

    /**
     * @dataProvider provideCreateDepositPaymentSessionData
     */
    public function testUnitsNotFoundException(CreateDepositPaymentSession $command, Reservation $reservation): void
    {
        $this->expectException(UnitsNotFoundException::class);

        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn($reservation);

        $this->unitRepository
            ->expects(self::once())
            ->method('getByIds')
            ->willReturn(null);

        $this->stripeApiClient
            ->expects(self::never())
            ->method('createCheckoutSession');

        $this->depositPaymentSessionRepository
            ->expects(self::never())
            ->method('save');

        $this->handler->handle($command);
    }

    /**
     * @dataProvider provideCreateDepositPaymentSessionData
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testHandleWithDepositDisabledException(CreateDepositPaymentSession $command): void
    {
        $depositPaymentSession = Mockery::mock('alias:'.DepositPaymentSession::class);
        $reservationUnitCollection = UnitCollection::fromUnits(
            new Unit(
                UnitId::fromInt(1),
                UnitDeposit::fromFloat(0.0)
            )
        );

        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn(
                new Reservation(
                    ReservationId::fromString('1234-1234-1234'),
                    AgentId::fromString('b72a7cdc-0d36-411e-b897-c9dd0a79c318'),
                    DepositTransferDeadline::fromSeconds(1653004800),
                    Language::fromString('de'),
                    ProjectId::fromInt(80262),
                    ProspectId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0'),
                    $reservationUnitCollection->totalUnitDeposit(),
                    $reservationUnitCollection,
                )
            );

        $this->unitRepository
            ->expects(self::once())
            ->method('getByIds')
            ->willReturn(UnitReadModelCollection::fromUnits(
                new UnitReadModel(
                    UnitId::fromInt(1),
                    UnitName::fromString('WE 1')
                )
            ));

        $depositPaymentSession
            ->shouldReceive('createNewDepositPaymentSession')
            ->andThrow(DepositDisabledException::class);

        $this->logger
            ->expects(self::once())
            ->method('info');

        $this->stripeApiClient
            ->expects(self::never())
            ->method('createCheckoutSession');

        $this->depositPaymentSessionRepository
            ->expects(self::never())
            ->method('save');

        $this->handler->handle($command);
    }

    public function provideCreateDepositPaymentSessionData(): Generator
    {
        $reservationId = ReservationId::fromString('1234-1234-1234');
        $reservationUnitCollection = UnitCollection::fromUnits(
            new Unit(
                UnitId::fromInt(1),
                UnitDeposit::fromFloat(3000.0)
            )
        );

        yield 'CreateDepositPaymentSessionCommandHandler data' => [
            new CreateDepositPaymentSession(
                CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0'),
                $reservationId
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
            CheckoutSession::fromArray([
                CheckoutSession::CHECKOUT_SESSION_ID => 'cs_test_a1TpDn0YJ3sKoj7lbOq1DbWGrnGQMPT4Kdg837nN8pyFarROhHuvOp71BC',
                CheckoutSession::CHECKOUT_SESSION_STATUS => 'open',
                CheckoutSession::CHECKOUT_SESSION_URL => 'https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl',
                CheckoutSession::CURRENCY => 'eur',
                CheckoutSession::CUSTOMER_ID => 'customer id',
                CheckoutSession::EXPIRES_AT => 1653004800,
                CheckoutSession::PAYMENT_INTENT_ID => 'pi_3KSOXmJHRV8spf0Q1Vaclh9l',
                CheckoutSession::PAYMENT_STATUS => 'unpaid',
            ]),
        ];
    }
}
