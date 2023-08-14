<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\RetryDepositPaymentSessionCreation\Command;

use Allmyhomes\Application\UseCase\PopulateReservation\Document\Reservation as ReservationReadModel;
use Allmyhomes\Application\UseCase\PopulateReservation\Repository\ReservationRepositoryInterface;
use Allmyhomes\Application\UseCase\PopulateUnit\Document\Unit as UnitReadModel;
use Allmyhomes\Application\UseCase\PopulateUnit\Repository\UnitRepositoryInterface;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command\RetryDepositPaymentSessionCreation;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command\RetryDepositPaymentSessionCreationHandler;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Exception\DepositPaymentSessionNotFoundException;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Exception\ReservationNotFoundException;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Exception\UnitsNotFoundException;
use Allmyhomes\Domain\DepositPaymentSession\Aggregate\DepositPaymentSession;
use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Allmyhomes\Domain\DepositPaymentSession\Repository\DepositPaymentSessionRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSession;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\Reservation\ValueObject\Unit;
use Allmyhomes\Domain\Reservation\ValueObject\UnitCollection as ReservationUnitReadModelCollection;
use Allmyhomes\Domain\Unit\ValueObject\UnitCollection as UnitReadModelCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Domain\ValueObject\UnitDeposit;
use Allmyhomes\Domain\ValueObject\UnitId;
use Allmyhomes\Domain\ValueObject\UnitName;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RetryDepositPaymentSessionCreationHandlerTest extends TestCase
{
    private RetryDepositPaymentSessionCreationHandler $handler;

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

    public function setUp(): void
    {
        parent::setUp();

        $this->depositPaymentSessionRepository = $this->createMock(DepositPaymentSessionRepositoryInterface::class);
        $this->stripeApiClient = $this->createMock(StripeServiceInterface::class);
        $this->reservationRepository = $this->createMock(ReservationRepositoryInterface::class);
        $this->unitRepository = $this->createMock(UnitRepositoryInterface::class);

        $this->handler = new RetryDepositPaymentSessionCreationHandler(
            $this->depositPaymentSessionRepository,
            $this->stripeApiClient,
            $this->reservationRepository,
            $this->unitRepository,
        );
    }

    /**
     * @dataProvider provideRetryDepositPaymentSessionCreationData
     */
    public function testHandle(
        RetryDepositPaymentSessionCreation $command,
        ReservationReadModel $reservation,
        UnitReadModelCollection $unitReadModelCollection,
        CheckoutSession $checkoutSession
    ): void {
        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn($reservation);

        $this->unitRepository
            ->expects(self::once())
            ->method('getByIds')
            ->willReturn($unitReadModelCollection);

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
     * @dataProvider provideRetryDepositPaymentSessionCreationData
     */
    public function testReservationNotFoundException(RetryDepositPaymentSessionCreation $command): void
    {
        $this->expectException(ReservationNotFoundException::class);

        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn(null);

        $this->handler->handle($command);
    }

    /**
     * @dataProvider provideRetryDepositPaymentSessionCreationData
     */
    public function testUnitsNotFoundException(RetryDepositPaymentSessionCreation $command, ReservationReadModel $reservation): void
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

        $this->handler->handle($command);
    }

    /**
     * @dataProvider provideRetryDepositPaymentSessionCreationData
     */
    public function testDepositPaymentSessionNotFoundException(
        RetryDepositPaymentSessionCreation $command,
        ReservationReadModel $reservation,
        UnitReadModelCollection $unitCollection,
    ): void {
        $this->expectException(DepositPaymentSessionNotFoundException::class);

        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn($reservation);

        $this->unitRepository
            ->expects(self::once())
            ->method('getByIds')
            ->willReturn($unitCollection);

        $this->depositPaymentSessionRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn(null);

        $this->handler->handle($command);
    }

    public function provideRetryDepositPaymentSessionCreationData(): Generator
    {
        $unitId = 1;
        $unitDeposit = 3000.00;
        $unitName = 'WE 1';
        $reservationId = ReservationId::fromString('1234-1234-1234');
        $reservationUnitCollection = ReservationUnitReadModelCollection::fromUnits(
            new Unit(
                UnitId::fromInt($unitId),
                UnitDeposit::fromFloat($unitDeposit)
            )
        );
        $errorCount = ErrorCount::fromInt(1);

        $unitReadModelCollection = UnitReadModelCollection::fromUnits(
            new UnitReadModel(
                UnitId::fromInt($unitId),
                UnitName::fromString($unitName)
            ),
        );

        yield 'RetryDepositPaymentSessionCreationCommandHandler data with single unit' => [
            new RetryDepositPaymentSessionCreation(
                CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0'),
                $reservationId,
                $errorCount,
            ),
            new ReservationReadModel(
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

        $unitId2 = 2;
        $unitDeposit2 = 6000.00;
        $unitName2 = 'WE 2';

        $reservationUnitCollection->add(
            new Unit(
                UnitId::fromInt($unitId2),
                UnitDeposit::fromFloat($unitDeposit2)
            )
        );
        $unitReadModelCollection->add(
            new UnitReadModel(
                UnitId::fromInt($unitId2),
                UnitName::fromString($unitName2)
            )
        );

        yield 'RetryDepositPaymentSessionCreationCommandHandler data with multiple units' => [
            new RetryDepositPaymentSessionCreation(
                CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0'),
                $reservationId,
                $errorCount,
            ),
            new ReservationReadModel(
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
