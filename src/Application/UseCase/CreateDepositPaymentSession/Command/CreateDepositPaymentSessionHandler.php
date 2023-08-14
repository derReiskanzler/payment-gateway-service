<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command;

use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Exception\ReservationNotFoundException;
use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Exception\UnitsNotFoundException;
use Allmyhomes\Application\UseCase\PopulateReservation\Repository\ReservationRepositoryInterface;
use Allmyhomes\Application\UseCase\PopulateUnit\Repository\UnitRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\Aggregate\DepositPaymentSession;
use Allmyhomes\Domain\DepositPaymentSession\Exception\DepositDisabledException;
use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Allmyhomes\Domain\DepositPaymentSession\Repository\DepositPaymentSessionRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Psr\Log\LoggerInterface;

final class CreateDepositPaymentSessionHandler implements CreateDepositPaymentSessionHandlerInterface
{
    public function __construct(
        private DepositPaymentSessionRepositoryInterface $depositPaymentSessionRepository,
        private StripeServiceInterface $stripeService,
        private ReservationRepositoryInterface $reservationRepository,
        private UnitRepositoryInterface $unitRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(CreateDepositPaymentSession $command): void
    {
        $reservation = $this->reservationRepository->getById($command->reservationId());

        if (empty($reservation)) {
            throw ReservationNotFoundException::forReservationId($command->reservationId());
        }

        $unitCollection = $this->unitRepository->getByIds($reservation->units()->idCollection());

        if (empty($unitCollection)) {
            throw UnitsNotFoundException::forUnitIds($reservation->units()->idCollection());
        }

        try {
            $depositPaymentSessionAggregate = DepositPaymentSession::createNewDepositPaymentSession(
                $reservation->id(),
                $reservation->agentId(),
                $reservation->projectId(),
                $reservation->prospectId(),
                $reservation->language(),
                $reservation->depositTransferDeadline(),
                UnitCollection::fromReadModelUnitCollections($unitCollection, $reservation->units()),
                $this->stripeService,
            );
        } catch (DepositDisabledException $e) {
            $this->logger->info($e->getMessage());

            return;
        }

        $this->depositPaymentSessionRepository->save($depositPaymentSessionAggregate, $command);
    }
}
