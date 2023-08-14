<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheReservationProjectionRunsTrait;

trait GivenReservationExistsTrait
{
    use GivenAgentInitiatedReservationTrait;
    use WhenTheReservationProjectionRunsTrait;

    /**
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final public function givenReservationExists(
        string $id,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        float $totalUnitDeposit,
        array $units
    ): void {
        $this->givenAgentInitiatedReservation(
            $id,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $totalUnitDeposit,
            $units,
        );
        $this->whenTheReservationProjectionRuns();
    }
}
