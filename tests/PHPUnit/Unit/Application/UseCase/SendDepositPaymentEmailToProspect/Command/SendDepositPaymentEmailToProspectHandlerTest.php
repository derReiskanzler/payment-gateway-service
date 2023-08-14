<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\SendDepositPaymentEmailToProspect\Command;

use Allmyhomes\Application\UseCase\PopulateProspect\Document\Prospect;
use Allmyhomes\Application\UseCase\PopulateProspect\Repository\ProspectRepositoryInterface;
use Allmyhomes\Application\UseCase\PopulateReservation\Document\Reservation;
use Allmyhomes\Application\UseCase\PopulateReservation\Repository\ReservationRepositoryInterface;
use Allmyhomes\Application\UseCase\PopulateUnit\Document\Unit as UnitReadModel;
use Allmyhomes\Application\UseCase\PopulateUnit\Repository\UnitRepositoryInterface;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command\SendDepositPaymentEmailToProspect;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command\SendDepositPaymentEmailToProspectHandler;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception\ProspectNotFoundException;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception\ReservationNotFoundException;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception\UnitsNotFoundException;
use Allmyhomes\Domain\DepositPaymentEmail\Mail\MailerInterface;
use Allmyhomes\Domain\DepositPaymentEmail\Repository\DepositPaymentEmailRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectEmail;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectFirstName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectLastName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectSalutation;
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

final class SendDepositPaymentEmailToProspectHandlerTest extends TestCase
{
    private SendDepositPaymentEmailToProspectHandler $handler;

    /**
     * @var MailerInterface&MockObject
     */
    private MailerInterface $mailer;

    /**
     * @var ProspectRepositoryInterface&MockObject
     */
    private ProspectRepositoryInterface $prospectRepository;

    /**
     * @var UnitRepositoryInterface&MockObject
     */
    private UnitRepositoryInterface $unitRepository;

    /**
     * @var ReservationRepositoryInterface&MockObject
     */
    private ReservationRepositoryInterface $reservationRepository;

    /**
     * @var DepositPaymentEmailRepositoryInterface&MockObject
     */
    private DepositPaymentEmailRepositoryInterface $depositPaymentEmailRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->mailer = $this->createMock(MailerInterface::class);
        $this->prospectRepository = $this->createMock(ProspectRepositoryInterface::class);
        $this->unitRepository = $this->createMock(UnitRepositoryInterface::class);
        $this->reservationRepository = $this->createMock(ReservationRepositoryInterface::class);
        $this->depositPaymentEmailRepository = $this->createMock(DepositPaymentEmailRepositoryInterface::class);

        $this->handler = new SendDepositPaymentEmailToProspectHandler(
            $this->mailer,
            $this->prospectRepository,
            $this->unitRepository,
            $this->reservationRepository,
            $this->depositPaymentEmailRepository,
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * @dataProvider provideSendDepositPaymentEmailToProspectData
     */
    public function testHandle(
        SendDepositPaymentEmailToProspect $command,
        Reservation $reservation,
        Prospect $prospect,
    ): void {
        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn($reservation);

        $this->prospectRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn($prospect);

        $this->unitRepository
            ->expects(self::once())
            ->method('getByIds')
            ->willReturn(UnitReadModelCollection::fromUnits(
                new UnitReadModel(
                    UnitId::fromInt(1),
                    UnitName::fromString('WE 1')
                )
            ));

        $this->depositPaymentEmailRepository
            ->expects(self::once())
            ->method('save');

        $this->handler->handle($command);
    }

    /**
     * @dataProvider provideSendDepositPaymentEmailToProspectData
     */
    public function testReservationNotFoundException(SendDepositPaymentEmailToProspect $command): void
    {
        $this->expectException(ReservationNotFoundException::class);

        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn(null);

        $this->prospectRepository
            ->expects(self::never())
            ->method('getById');

        $this->unitRepository
            ->expects(self::never())
            ->method('getByIds');

        $this->depositPaymentEmailRepository
            ->expects(self::never())
            ->method('save');

        $this->handler->handle($command);
    }

    /**
     * @dataProvider provideSendDepositPaymentEmailToProspectData
     */
    public function testProspectNotFoundException(SendDepositPaymentEmailToProspect $command, Reservation $reservation): void
    {
        $this->expectException(ProspectNotFoundException::class);

        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn($reservation);

        $this->prospectRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn(null);

        $this->unitRepository
            ->expects(self::never())
            ->method('getByIds');

        $this->depositPaymentEmailRepository
            ->expects(self::never())
            ->method('save');

        $this->handler->handle($command);
    }

    /**
     * @dataProvider provideSendDepositPaymentEmailToProspectData
     */
    public function testUnitsNotFoundException(
        SendDepositPaymentEmailToProspect $command,
        Reservation $reservation,
        Prospect $prospect
    ): void {
        $this->expectException(UnitsNotFoundException::class);

        $this->reservationRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn($reservation);

        $this->prospectRepository
            ->expects(self::once())
            ->method('getById')
            ->willReturn($prospect);

        $this->unitRepository
            ->expects(self::once())
            ->method('getByIds')
            ->willReturn(null);

        $this->depositPaymentEmailRepository
            ->expects(self::never())
            ->method('save');

        $this->handler->handle($command);
    }

    public function provideSendDepositPaymentEmailToProspectData(): Generator
    {
        $reservationId = ReservationId::fromString('1234-1234-1234');

        $reservationUnitCollection = UnitCollection::fromUnits(
            new Unit(
                UnitId::fromInt(1),
                UnitDeposit::fromFloat(3000.0)
            )
        );

        yield 'SendDepositPaymentEmailToProspectCommandHandler data' => [
            new SendDepositPaymentEmailToProspect(
                CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0'),
                $reservationId,
                CheckoutSessionId::fromString('cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2'),
                CheckoutSessionUrl::fromString('https://www.example.com'),
                ExpiresAt::fromSeconds(1653004800),
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
            new Prospect(
                ProspectId::fromString('ca50819f-e5a4-40d3-a425-daba3e095407'),
                ProspectEmail::fromString('max.mustermann@gmail.com'),
                ProspectFirstName::fromString('Max'),
                ProspectLastName::fromString('Mustermann'),
                ProspectSalutation::fromInt(0),
            ),
        ];
    }
}
