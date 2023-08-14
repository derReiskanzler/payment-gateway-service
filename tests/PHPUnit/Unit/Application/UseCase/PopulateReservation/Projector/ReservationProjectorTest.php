<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\PopulateReservation\Projector;

use Allmyhomes\Application\UseCase\PopulateReservation\Document\Reservation;
use Allmyhomes\Application\UseCase\PopulateReservation\Projector\ReservationsProjector;
use Allmyhomes\Application\UseCase\PopulateReservation\Repository\ReservationRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\Reservation\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Domain\ValueObject\TotalUnitDeposit;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ReservationProjectorTest extends TestCase
{
    /**
     * @var MockObject&ReservationRepositoryInterface
     */
    private MockObject $repository;
    private ReservationsProjector $projector;

    public function setUp(): void
    {
        $this->repository = $this->createMock(ReservationRepositoryInterface::class);
        $this->projector = new ReservationsProjector($this->repository);
    }

    /**
     * @throws \Exception
     *
     * @dataProvider provideReservationEvents
     */
    public function testHandleAgentInitiatedReservationEvent(EventDTO $event, Reservation $reservation): void
    {
        $this->repository
            ->expects($this->once())
            ->method('upsert')
            ->with($reservation);

        $this->projector->handle($event);
    }

    /**
     * @throws \Exception
     *
     * @dataProvider provideOtherEvent
     */
    public function testHandleOtherEvent(EventDTO $event): void
    {
        $this->repository
            ->expects($this->never())
            ->method('upsert');

        $this->projector->handle($event);
    }

    /**
     * @return Generator<mixed>
     */
    public function provideReservationEvents(): Generator
    {
        $reservationId = '1111-2222-3333';
        $agentId = 'da7c58f5-4c74-4722-8b94-7fcf8d857055';
        $depositTransferDeadline = '2020-06-27T21:37:45.531877';
        $language = 'de';
        $projectId = 80262;
        $prospectId = 'ca50819f-e5a4-40d3-a425-daba3e095407';
        $unitId = 4321;
        $unitPrice = 170000.00;
        $unitDeposit = 400.00;
        $totalDeposit = $unitDeposit;
        $totalPrice = $unitPrice;
        $unit1 = [
            'id' => $unitId,
            'price' => [
                'value' => $unitPrice,
                'currency' => 'EUR',
            ],
            'deposit' => $unitDeposit,
        ];

        $units = [
            $unit1,
        ];

        yield 'AgentInitiatedReservation with full payload' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'ReservationManagement.AgentInitiatedReservation',
                [
                    'id' => $reservationId,
                    'agent_id' => $agentId,
                    'prospect_id' => $prospectId,
                    'project_id' => $projectId,
                    'units' => $units,
                    'total_deposit' => $totalDeposit,
                    'deposit_transfer_deadline' => $depositTransferDeadline,
                    'total_unit_price' => $totalPrice,
                    'status' => 'INITIATED',
                    'language' => $language,
                    'occurred_at' => '2022-03-14T21:37:45.531877',
                ],
                []
            ),
            new Reservation(
                ReservationId::fromString($reservationId),
                AgentId::fromString($agentId),
                DepositTransferDeadline::fromString($depositTransferDeadline),
                Language::fromString($language),
                ProjectId::fromInt($projectId),
                ProspectId::fromString($prospectId),
                TotalUnitDeposit::fromFloat($totalDeposit),
                UnitCollection::fromArray($units),
            ),
        ];

        $unitId2 = 4322;
        $unitPrice2 = 200000.00;
        $unitDeposit2 = 500.00;
        $totalDeposit = $unitDeposit + $unitDeposit2;
        $totalPrice = $unitPrice + $unitPrice2;

        $unit2 = [
            'id' => $unitId2,
            'price' => [
                'value' => $unitPrice2,
                'currency' => 'EUR',
            ],
            'deposit' => $unitDeposit2,
        ];

        $units = [
            $unit1,
            $unit2,
        ];

        yield 'AgentInitiatedReservation with full payload and multiple units' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'ReservationManagement.AgentInitiatedReservation',
                [
                    'id' => $reservationId,
                    'agent_id' => $agentId,
                    'prospect_id' => $prospectId,
                    'project_id' => $projectId,
                    'units' => $units,
                    'total_deposit' => $totalDeposit,
                    'deposit_transfer_deadline' => $depositTransferDeadline,
                    'total_unit_price' => $totalPrice,
                    'status' => 'INITIATED',
                    'language' => $language,
                    'occurred_at' => '2022-03-14T21:37:45.531877',
                ],
                []
            ),
            new Reservation(
                ReservationId::fromString($reservationId),
                AgentId::fromString($agentId),
                DepositTransferDeadline::fromString($depositTransferDeadline),
                Language::fromString($language),
                ProjectId::fromInt($projectId),
                ProspectId::fromString($prospectId),
                TotalUnitDeposit::fromFloat($totalDeposit),
                UnitCollection::fromArray($units),
            ),
        ];

        yield 'AgentInitiatedReservation payload without optionals' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'ReservationManagement.AgentInitiatedReservation',
                [
                    'id' => $reservationId,
                    'agent_id' => $agentId,
                    'prospect_id' => $prospectId,
                    'project_id' => $projectId,
                    'units' => $units,
                    'total_deposit' => $totalDeposit,
                    'deposit_transfer_deadline' => null,
                    'total_unit_price' => $totalPrice,
                    'status' => 'INITIATED',
                    'language' => $language,
                    'occurred_at' => '2022-03-14T21:37:45.531877',
                ],
                []
            ),
            new Reservation(
                ReservationId::fromString($reservationId),
                AgentId::fromString($agentId),
                null,
                Language::fromString($language),
                ProjectId::fromInt($projectId),
                ProspectId::fromString($prospectId),
                TotalUnitDeposit::fromFloat($totalDeposit),
                UnitCollection::fromArray($units),
            ),
        ];
    }

    /**
     * @return Generator<mixed>
     */
    public function provideOtherEvent(): Generator
    {
        yield 'Other Event with empty payload' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'Other.Event',
                [],
                []
            ),
        ];
    }
}
