<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\PopulateReservation\Projector;

use Allmyhomes\Application\UseCase\PopulateReservation\Document\Reservation;
use Allmyhomes\Application\UseCase\PopulateReservation\Repository\ReservationRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\Reservation\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Domain\ValueObject\TotalUnitDeposit;
use Allmyhomes\EventProjections\Contracts\EventHandlers\EventHandlerInterface;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;

final class ReservationsProjector implements EventHandlerInterface
{
    private const RESERVATION_MANAGEMENT_AGENT_INITIATED_RESERVATION = 'ReservationManagement.AgentInitiatedReservation';

    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    ) {
    }

    public function handle(EventDTO $event): void
    {
        switch ($event->getName()) {
            case self::RESERVATION_MANAGEMENT_AGENT_INITIATED_RESERVATION:
                $this->handleAgentInitiatedReservationEvent($event);
                break;
            default:
                break;
        }
    }

    public function handleAgentInitiatedReservationEvent(EventDTO $event): void
    {
        $payload = $event->getPayload();

        $this->reservationRepository->upsert(
            new Reservation(
                ReservationId::fromString($payload['id']),
                AgentId::fromString($payload['agent_id']),
                $this->getDepositTransferDeadline($payload),
                Language::fromString($payload['language']),
                ProjectId::fromInt($payload['project_id']),
                ProspectId::fromString($payload['prospect_id']),
                TotalUnitDeposit::fromFloat($payload['total_deposit']),
                UnitCollection::fromArray($payload['units']),
            )
        );
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function getDepositTransferDeadline(array $payload): ?DepositTransferDeadline
    {
        return isset($payload['deposit_transfer_deadline']) ? DepositTransferDeadline::fromString($payload['deposit_transfer_deadline']) : null;
    }
}
