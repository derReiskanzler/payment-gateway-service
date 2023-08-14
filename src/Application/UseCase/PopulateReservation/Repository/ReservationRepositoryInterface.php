<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\PopulateReservation\Repository;

use Allmyhomes\Application\UseCase\PopulateReservation\Document\Reservation;
use Allmyhomes\Domain\ValueObject\ReservationId;

interface ReservationRepositoryInterface
{
    public function upsert(Reservation $reservation): void;

    public function getById(ReservationId $reservationId): ?Reservation;
}
