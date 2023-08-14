<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\UseCase\CreateDepositPaymentSession;

use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Exception\ReservationNotFoundException;
use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Exception\UnitsNotFoundException;
use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\Given\GivenReservationAcceptedByInventoryTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenReservationExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenStripeApiClientReturnsCheckoutSession;
use Tests\PHPUnit\Integration\Scenario\Given\GivenStripeApiClientReturnsEmptyCheckoutSession;
use Tests\PHPUnit\Integration\Scenario\Given\GivenUnitsExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenDepositPaymentSessionShouldExistTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenDepositPaymentSessionShouldNotExistTrait;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheReservationAcceptedProjectionRunsTrait;
use Tests\PHPUnit\IntegrationTestCase;

final class HandleReservationAcceptedByInventoryTest extends IntegrationTestCase
{
    use GivenUnitsExistsTrait;
    use GivenReservationExistsTrait;
    use GivenStripeApiClientReturnsCheckoutSession;
    use GivenReservationAcceptedByInventoryTrait;
    use GivenStripeApiClientReturnsEmptyCheckoutSession;
    use WhenTheReservationAcceptedProjectionRunsTrait;
    use ThenDepositPaymentSessionShouldExistTrait;
    use ThenDepositPaymentSessionShouldNotExistTrait;

    /**
     * @param int[]             $unitIds
     * @param string[]          $unitNames
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideReservationAcceptedByInventoryData
     */
    public function testHandle(
        string $reservationId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
        string $checkoutSessionId,
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
        $this->thenDepositPaymentSessionShouldExist(
            $reservationId,
            $checkoutSessionId,
            0,
            'open',
            'unpaid',
        );
    }

    /**
     * @param int[]             $unitIds
     * @param string[]          $unitNames
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideReservationAcceptedByInventoryData
     */
    public function testHandleWithApiError(
        string $reservationId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
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

        $this->givenStripeApiClientReturnsEmptyCheckoutSession();
        $this->givenReservationAcceptedByInventory(
            $reservationId,
            $unitIds
        );
        $this->whenTheReservationAcceptedProjectionRuns();
        $this->thenDepositPaymentSessionShouldExist(
            $reservationId,
            null,
            1
        );
    }

    /**
     * @param int[] $unitIds
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideReservationAcceptedByInventoryData
     */
    public function testHandleWithNoReservationReadModel(
        string $reservationId,
        array $unitIds,
    ): void {
        $this->expectException(ReservationNotFoundException::class);

        $this->givenReservationAcceptedByInventory(
            $reservationId,
            $unitIds
        );
        $this->whenTheReservationAcceptedProjectionRuns();
        $this->thenDepositPaymentSessionShouldNotExist(
            $reservationId,
            null,
            1
        );
    }

    /**
     * @param int[]             $unitIds
     * @param string[]          $unitNames
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideReservationAcceptedByInventoryData
     */
    public function testHandleWithNoUnitReadModel(
        string $reservationId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
    ): void {
        $this->expectException(UnitsNotFoundException::class);

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
        $this->givenReservationAcceptedByInventory(
            $reservationId,
            $unitIds
        );
        $this->whenTheReservationAcceptedProjectionRuns();
        $this->thenDepositPaymentSessionShouldNotExist(
            $reservationId,
            null,
            1
        );
    }

    /**
     * @param int[]             $unitIds
     * @param string[]          $unitNames
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideReservationAcceptedByInventoryData
     */
    public function testHandleWithNoDeposit(
        string $reservationId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        array $unitNames,
        array $units,
    ): void {
        $unitsWithNoDeposit = [];
        foreach ($units as $unit) {
            $unit['deposit'] = 0;
            $unitsWithNoDeposit[] = $unit;
        }

        $this->givenUnitsExists($unitIds, $unitNames);
        $this->givenReservationExists(
            $reservationId,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            0,
            $unitsWithNoDeposit,
        );
        $this->givenReservationAcceptedByInventory(
            $reservationId,
            $unitIds
        );
        $this->whenTheReservationAcceptedProjectionRuns();
        $this->thenDepositPaymentSessionShouldNotExist(
            $reservationId,
            null,
            1
        );
    }

    public function provideReservationAcceptedByInventoryData(): Generator
    {
        $partialPurposeId1 = $this->faker()->numberBetween(1000, 9999);
        $partialPurposeId2 = $this->faker()->numberBetween(1000, 9999);
        $partialPurposeId3 = $this->faker()->numberBetween(1000, 9999);
        $reservationId = $partialPurposeId1.'-'.$partialPurposeId2.'-'.$partialPurposeId3;

        $agentId = 'da7c58f5-4c74-4722-8b94-7fcf8d857055';
        $depositTransferDeadline = '2020-07-16T16:00:00+00:00';
        $language = 'de';
        $projectId = 80262;
        $prospectId = 'ca50819f-e5a4-40d3-a425-daba3e095407';

        $unitId = $this->faker()->numberBetween(1000, 9999);
        $unitName = $this->faker()->text(10);
        $unitDeposit = $this->faker()->randomFloat(2, 1000, 9999);
        $totalUnitDeposit = $unitDeposit;

        $checkoutSessionId = 'cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2';

        $unitIds = [
            0 => $unitId,
        ];
        $unitNames = [
            0 => $unitName,
        ];
        $units = [
            0 => [
                'id' => $unitId,
                'deposit' => $unitDeposit,
            ],
        ];

        yield 'Reservation Accepted By Inventory Data with single unit' => [
            $reservationId,
            $unitIds,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $unitNames,
            $units,
            $totalUnitDeposit,
            $checkoutSessionId,
        ];

        $otherUnitId = $this->faker()->numberBetween(1000, 9999);
        $otherUnitName = $this->faker()->text(10);
        $otherUnitDeposit = $this->faker()->randomFloat(2, 1000, 9999);
        $totalUnitDeposit = $totalUnitDeposit + $otherUnitDeposit;

        $unitIds = array_merge($unitIds, [0 => $otherUnitId]);
        $unitNames = array_merge($unitNames, [0 => $otherUnitName]);
        $units = array_merge(
            $units,
            [
                0 => [
                    'id' => $otherUnitId,
                    'deposit' => $otherUnitDeposit,
                ],
            ]
        );

        yield 'Reservation Accepted By Inventory Data with multiple units' => [
            $reservationId,
            $unitIds,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $unitNames,
            $units,
            $totalUnitDeposit,
            $checkoutSessionId,
        ];
    }
}
