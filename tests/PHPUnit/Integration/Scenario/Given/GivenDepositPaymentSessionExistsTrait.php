<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheReservationAcceptedProjectionRunsTrait;

trait GivenDepositPaymentSessionExistsTrait
{
    use GivenReservationAcceptedByInventoryTrait;
    use GivenReservationExistsTrait;
    use GivenUnitsExistsTrait;
    use GivenStripeApiClientReturnsCheckoutSession;
    use WhenTheReservationAcceptedProjectionRunsTrait;

    /**
     * @param int[]             $unitIds
     * @param string[]          $unitNames
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final public function givenDepositPaymentSessionExists(
        string $reservationId,
        array $unitIds,
        array $unitNames,
        string $checkoutSessionId,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        float $totalUnitDeposit,
        array $units,
    ): void {
        $this->givenUnitsExists($unitIds, $unitNames);
        $this->givenReservationExists(
            $reservationId,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $totalUnitDeposit,
            $units,
        );
        $this->givenStripeApiClientReturnsCheckoutSession($checkoutSessionId);
        $this->givenReservationAcceptedByInventory(
            $reservationId,
            $unitIds,
        );
        $this->whenTheReservationAcceptedProjectionRuns();
    }
}
