<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Exception;

use Allmyhomes\Domain\ValueObject\UnitIdCollection;
use LogicException;

final class UnitsNotFoundException extends LogicException
{
    public static function forUnitIds(UnitIdCollection $unitIds): self
    {
        return new self(
            sprintf('No unit(s) for unit ids: [%s]', join(', ', $unitIds->toArray())),
            404
        );
    }
}
