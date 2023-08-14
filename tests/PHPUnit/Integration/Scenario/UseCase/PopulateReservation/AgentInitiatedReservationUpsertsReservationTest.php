<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\UseCase\PopulateReservation;

use Allmyhomes\Domain\Context;
use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\Given\GivenAgentInitiatedReservationTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenReservationShouldExistTrait;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheReservationProjectionRunsTrait;
use Tests\PHPUnit\IntegrationTestCase;

final class AgentInitiatedReservationUpsertsReservationTest extends IntegrationTestCase
{
    use GivenAgentInitiatedReservationTrait;
    use ThenReservationShouldExistTrait;
    use WhenTheReservationProjectionRunsTrait;

    /**
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideAgentInitiatedReservationData
     */
    public function testUpsert(
        string $reservationId,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        float $totalUnitDeposit,
        array $units
    ): void {
        $expectedUnits = $units;
        unset($expectedUnits[0]['price']);

        $this->givenAgentInitiatedReservation(
            $reservationId,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $totalUnitDeposit,
            $units
        );
        $this->whenTheReservationProjectionRuns();
        $this->thenReservationShouldExist(
            $reservationId,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $totalUnitDeposit,
            $expectedUnits
        );
    }

    public function provideAgentInitiatedReservationData(): Generator
    {
        $partialPurposeId1 = $this->faker()->numberBetween(1000, 9999);
        $partialPurposeId2 = $this->faker()->numberBetween(1000, 9999);
        $partialPurposeId3 = $this->faker()->numberBetween(1000, 9999);
        $reservationId = $partialPurposeId1.'-'.$partialPurposeId2.'-'.$partialPurposeId3;

        $agentId = 'da7c58f5-4c74-4722-8b94-7fcf8d857055';
        $depositTransferDeadline = $this->faker()->date(Context::DEFAULT_TIME_FORMAT);
        $language = 'de';
        $projectId = 80262;
        $prospectId = 'ca50819f-e5a4-40d3-a425-daba3e095407';

        $unitId = $this->faker()->numberBetween(1000, 9999);
        $unitDeposit = $this->faker()->randomFloat(2, 1000, 5000);
        $totalUnitDeposit = $unitDeposit;

        $units = [
            0 => [
                'id' => $unitId,
                'price' => [
                    'value' => 170000.00,
                    'currency' => 'EUR',
                ],
                'deposit' => $unitDeposit,
            ],
        ];

        yield 'AgentInitiatedReservation with full payload' => [
            'reservation id' => $reservationId,
            'agent id' => $agentId,
            'deposit transfer deadline' => $depositTransferDeadline,
            'language' => $language,
            'project id' => $projectId,
            'prospect id' => $prospectId,
            'total unit deposit' => $totalUnitDeposit,
            'units' => $units,
        ];

        $unitId2 = $this->faker()->numberBetween(1000, 9999);
        $unitDeposit2 = $this->faker()->randomFloat(2, 1000, 5000);

        $units = [
            0 => [
                'id' => $unitId,
                'price' => [
                    'value' => 170000.00,
                    'currency' => 'EUR',
                ],
                'deposit' => $unitDeposit,
            ],
            1 => [
                'id' => $unitId2,
                'price' => [
                    'value' => 280000.00,
                    'currency' => 'EUR',
                ],
                'deposit' => $unitDeposit2,
            ],
        ];

        yield 'AgentInitiatedReservation with multiple units' => [
            'reservation id' => $reservationId,
            'agent id' => $agentId,
            'deposit transfer deadline' => $depositTransferDeadline,
            'language' => $language,
            'project id' => $projectId,
            'prospect id' => $prospectId,
            'total unit deposit' => $totalUnitDeposit,
            'units' => $units,
        ];
    }
}
