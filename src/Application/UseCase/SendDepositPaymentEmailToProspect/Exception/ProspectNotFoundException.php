<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception;

use Allmyhomes\Domain\ValueObject\ProspectId;
use LogicException;

final class ProspectNotFoundException extends LogicException
{
    public static function forProspectid(ProspectId $prospectId): self
    {
        return new self(
            sprintf('prospect not found for id: \'[%s]\'', $prospectId->toString()),
            404
        );
    }
}
