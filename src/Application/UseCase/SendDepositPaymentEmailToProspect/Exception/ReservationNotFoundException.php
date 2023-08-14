<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception;

use Allmyhomes\Domain\ValueObject\ReservationId;
use LogicException;

final class ReservationNotFoundException extends LogicException
{
    public static function forReservationId(ReservationId $reservationId): self
    {
        return new self(
            sprintf('reservation not found for id: \'[%s]\'', $reservationId->toString()),
            404
        );
    }
}
