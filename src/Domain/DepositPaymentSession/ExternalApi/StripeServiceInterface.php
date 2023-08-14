<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ExternalApi;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSession;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;

interface StripeServiceInterface
{
    public function createCheckoutSession(
        ReservationId $reservationId,
        AgentId $agentId,
        ProjectId $projectId,
        ProspectId $prospectId,
        Language $language,
        UnitCollection $unitCollection,
        ?DepositTransferDeadline $depositTransferDeadline,
    ): ?CheckoutSession;
}
