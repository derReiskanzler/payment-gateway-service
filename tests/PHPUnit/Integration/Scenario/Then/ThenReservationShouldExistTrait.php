<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Then;

trait ThenReservationShouldExistTrait
{
    /**
     * @param array<int, mixed> $units
     */
    final protected function thenReservationShouldExist(
        string $id,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        float $totalUnitDeposit,
        array $units
    ): void {
        $expectedUnits = [];

        foreach ($units as $key => $unit) {
            $expectedUnits =
                array_merge(
                    $expectedUnits,
                    [
                        sprintf('doc->units->%d->id', $key) => $unit['id'],
                        sprintf('doc->units->%d->deposit', $key) => $unit['deposit'],
                    ]
                );
        }

        $this->assertDatabaseHas(
            'reservations',
            array_merge(
                [
                    'id' => $id,
                    'doc->id' => $id,
                    'doc->agent_id' => $agentId,
                    'doc->deposit_transfer_deadline' => $depositTransferDeadline,
                    'doc->language' => $language,
                    'doc->project_id' => $projectId,
                    'doc->prospect_id' => $prospectId,
                    'doc->total_unit_deposit' => $totalUnitDeposit,
                ],
                $expectedUnits
            )
        );
    }

    abstract public function assertDatabaseHas(string $string, array $array);
}
