<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\Exception;

use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use LogicException;

final class DepositDisabledException extends LogicException
{
    public static function forReservationId(ReservationId $reservationId, ProjectId $projectId): self
    {
        return new self(
            sprintf('deposit disabled for reservation id: \'[%s]\' and project id: %d.', $reservationId->toString(), $projectId->toInt()),
            409
        );
    }
}
