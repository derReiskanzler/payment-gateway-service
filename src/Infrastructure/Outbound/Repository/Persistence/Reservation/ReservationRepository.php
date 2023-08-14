<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Outbound\Repository\Persistence\Reservation;

use Allmyhomes\Application\UseCase\PopulateReservation\Document\Reservation;
use Allmyhomes\Application\UseCase\PopulateReservation\Repository\ReservationRepositoryInterface;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStoreReadModelRepository;

final class ReservationRepository extends DocumentStoreReadModelRepository implements ReservationRepositoryInterface
{
    private const COLLECTION_NAME = 'reservations';

    public function upsert(Reservation $reservation): void
    {
        $this->upsertDocument($reservation->id()->toString(), $reservation->toArray());
    }

    public function getById(ReservationId $reservationId): ?Reservation
    {
        $document = $this->getDocument($reservationId->toString());
        if (!$document) {
            return null;
        }

        return Reservation::fromArray($document);
    }

    public function collectionName(): string
    {
        return self::COLLECTION_NAME;
    }
}
