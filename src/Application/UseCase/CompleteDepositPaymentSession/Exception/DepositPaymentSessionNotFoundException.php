<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Exception;

use Allmyhomes\Domain\ValueObject\ReservationId;
use LogicException;

final class DepositPaymentSessionNotFoundException extends LogicException
{
    public static function forReservationId(ReservationId $reservationId): self
    {
        return new self(
            sprintf('deposit payment session not found for reservation id: \'[%s]\'', $reservationId->toString()),
            404
        );
    }
}
