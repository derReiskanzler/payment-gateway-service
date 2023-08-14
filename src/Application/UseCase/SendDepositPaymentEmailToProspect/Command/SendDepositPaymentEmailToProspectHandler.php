<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command;

use Allmyhomes\Application\UseCase\PopulateProspect\Repository\ProspectRepositoryInterface;
use Allmyhomes\Application\UseCase\PopulateReservation\Repository\ReservationRepositoryInterface;
use Allmyhomes\Application\UseCase\PopulateUnit\Repository\UnitRepositoryInterface;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception\ProspectNotFoundException;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception\ReservationNotFoundException;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception\UnitsNotFoundException;
use Allmyhomes\Domain\DepositPaymentEmail\Aggregate\DepositPaymentEmail;
use Allmyhomes\Domain\DepositPaymentEmail\Mail\MailerInterface;
use Allmyhomes\Domain\DepositPaymentEmail\Repository\DepositPaymentEmailRepositoryInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;

final class SendDepositPaymentEmailToProspectHandler implements SendDepositPaymentEmailToProspectHandlerInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private ProspectRepositoryInterface $prospectRepository,
        private UnitRepositoryInterface $unitRepository,
        private ReservationRepositoryInterface $reservationRepository,
        private DepositPaymentEmailRepositoryInterface $depositPaymentEmailRepository,
    ) {
    }

    public function handle(SendDepositPaymentEmailToProspect $command): void
    {
        $reservation = $this->reservationRepository->getById($command->reservationId());

        if (empty($reservation)) {
            throw ReservationNotFoundException::forReservationId($command->reservationId());
        }

        $prospect = $this->prospectRepository->getById($reservation->prospectId());

        if (empty($prospect)) {
            throw ProspectNotFoundException::forProspectId($reservation->prospectId());
        }

        $unitCollection = $this->unitRepository->getByIds($reservation->units()->idCollection());

        if (empty($unitCollection)) {
            throw UnitsNotFoundException::forUnitIds($reservation->units()->idCollection());
        }

        $depositPaymentEmailAggregate = DepositPaymentEmail::sendNewDepositPaymentEmail(
            $prospect->id(),
            $reservation->id(),
            $command->checkoutSessionId(),
            $prospect->email(),
            $prospect->firstName(),
            $prospect->lastName(),
            $prospect->salutation(),
            UnitCollection::fromReadModelUnitCollections($unitCollection, $reservation->units()),
            $command->checkoutSessionUrl(),
            $command->expiresAt(),
            $reservation->language(),
            $this->mailer,
        );

        $this->depositPaymentEmailRepository->save($depositPaymentEmailAggregate, $command);
    }
}
