<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command;

use Allmyhomes\Application\UseCase\PopulateReservation\Repository\ReservationRepositoryInterface;
use Allmyhomes\Application\UseCase\PopulateUnit\Repository\UnitRepositoryInterface;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Exception\DepositPaymentSessionNotFoundException;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Exception\ReservationNotFoundException;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Exception\UnitsNotFoundException;
use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Allmyhomes\Domain\DepositPaymentSession\Repository\DepositPaymentSessionRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;

final class RetryDepositPaymentSessionCreationHandler implements RetryDepositPaymentSessionCreationHandlerInterface
{
    public function __construct(
        private DepositPaymentSessionRepositoryInterface $depositPaymentSessionRepository,
        private StripeServiceInterface $stripeService,
        private ReservationRepositoryInterface $reservationRepository,
        private UnitRepositoryInterface $unitRepository,
    ) {
    }

    public function handle(RetryDepositPaymentSessionCreation $command): void
    {
        $reservation = $this->reservationRepository->getById($command->reservationId());

        if (empty($reservation)) {
            throw ReservationNotFoundException::forReservationId($command->reservationId());
        }

        $unitCollection = $this->unitRepository->getByIds($reservation->units()->idCollection());

        if (empty($unitCollection)) {
            throw UnitsNotFoundException::forUnitIds($reservation->units()->idCollection());
        }

        $depositPaymentSessionAggregate = $this->depositPaymentSessionRepository->getById($reservation->id());

        if (empty($depositPaymentSessionAggregate)) {
            throw DepositPaymentSessionNotFoundException::forReservationId($reservation->id());
        }

        $depositPaymentSessionAggregate->retryDepositPaymentSessionCreation(
            $reservation->id(),
            $reservation->agentId(),
            $reservation->projectId(),
            $reservation->prospectId(),
            $reservation->language(),
            $reservation->depositTransferDeadline(),
            UnitCollection::fromReadModelUnitCollections($unitCollection, $reservation->units()),
            $this->stripeService,
            $command->errorCount()
        );

        $this->depositPaymentSessionRepository->save($depositPaymentSessionAggregate, $command);
    }
}
