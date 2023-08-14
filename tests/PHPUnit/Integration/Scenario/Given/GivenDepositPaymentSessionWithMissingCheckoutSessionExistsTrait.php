<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheReservationAcceptedProjectionRunsTrait;

trait GivenDepositPaymentSessionWithMissingCheckoutSessionExistsTrait
{
    use GivenReservationAcceptedByInventoryTrait;
    use GivenStripeApiClientReturnsEmptyCheckoutSession;
    use WhenTheReservationAcceptedProjectionRunsTrait;

    /**
     * @param int[] $unitIds
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final public function givenDepositPaymentSessionWithMissingCheckoutSessionExists(
        string $reservationId,
        array $unitIds
    ): void {
        $this->givenStripeApiClientReturnsEmptyCheckoutSession();
        $this->givenReservationAcceptedByInventory(
            $reservationId,
            $unitIds,
        );
        $this->whenTheReservationAcceptedProjectionRuns();
    }
}
