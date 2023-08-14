<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\Exception;

use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ReservationId;
use LogicException;

final class CouldNotCreateCheckoutSessionException extends LogicException
{
    public static function forReservationId(ReservationId $reservationId, ErrorCount $errorCount): self
    {
        return new self(
            sprintf('could not create checkout session for reservation id: \'[%s]\' after %d attempts', $reservationId->toString(), $errorCount->toInt()),
            409
        );
    }
}
