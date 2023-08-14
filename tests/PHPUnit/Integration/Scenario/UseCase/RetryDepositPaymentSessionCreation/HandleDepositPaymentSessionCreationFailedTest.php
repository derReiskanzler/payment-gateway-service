<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\UseCase\RetryDepositPaymentSessionCreation;

use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Exception\DepositPaymentSessionNotFoundException;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Exception\ReservationNotFoundException;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Exception\UnitsNotFoundException;
use Allmyhomes\Domain\DepositPaymentSession\Exception\CouldNotCreateCheckoutSessionException;
use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\Given\GivenDepositPaymentSessionCreationFailedTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenDepositPaymentSessionWithMissingCheckoutSessionExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenReservationExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenStripeApiClientReturnsCheckoutSession;
use Tests\PHPUnit\Integration\Scenario\Given\GivenUnitsExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenDepositPaymentSessionShouldExistTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenDepositPaymentSessionShouldNotExistTrait;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheDepositPaymentSessionCreationFailedProjectionRunsTrait;
use Tests\PHPUnit\IntegrationTestCase;

final class HandleDepositPaymentSessionCreationFailedTest extends IntegrationTestCase
{
    use GivenUnitsExistsTrait;
    use GivenReservationExistsTrait;
    use GivenDepositPaymentSessionWithMissingCheckoutSessionExistsTrait;
    use GivenStripeApiClientReturnsCheckoutSession;
    use GivenDepositPaymentSessionCreationFailedTrait;
    use WhenTheDepositPaymentSessionCreationFailedProjectionRunsTrait;
    use ThenDepositPaymentSessionShouldExistTrait;
    use ThenDepositPaymentSessionShouldNotExistTrait;

    /**
     * @param int[]             $unitIds
     * @param string[]          $unitNames
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideDepositPaymentSessionCreationFailedData
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
        $this->givenDepositPaymentSessionWithMissingCheckoutSessionExists($reservationId, $unitIds);
        $this->thenDepositPaymentSessionShouldExist(
            $reservationId,
            null,
            1
        );

        $this->givenStripeApiClientReturnsCheckoutSession($checkoutSessionId);
        $this->givenDepositPaymentSessionCreationFailed(
            $reservationId,
            1,
        );
        $this->whenTheDepositPaymentSessionCreationFailedProjectionRuns();
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
     * @dataProvider provideDepositPaymentSessionCreationFailedData
     */
    public function testHandleWithOneApiError(
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
        $this->givenDepositPaymentSessionWithMissingCheckoutSessionExists($reservationId, $unitIds);
        $this->thenDepositPaymentSessionShouldExist(
            $reservationId,
            null,
            1
        );

        $this->givenStripeApiClientReturnsCheckoutSession($checkoutSessionId);
        $this->givenDepositPaymentSessionCreationFailed(
            $reservationId,
            1
        );
        $this->whenTheDepositPaymentSessionCreationFailedProjectionRuns();

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
     * @dataProvider provideDepositPaymentSessionCreationFailedData
     */
    public function testHandleWithApiErrorsOnly(
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
        $this->expectException(CouldNotCreateCheckoutSessionException::class);

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
        $this->givenDepositPaymentSessionWithMissingCheckoutSessionExists($reservationId, $unitIds);
        $this->thenDepositPaymentSessionShouldExist(
            $reservationId,
            null,
            1
        );
        $this->givenStripeApiClientReturnsEmptyCheckoutSession();
        $this->givenDepositPaymentSessionCreationFailed(
            $reservationId,
            1
        );
        $this->whenTheDepositPaymentSessionCreationFailedProjectionRuns();
        $this->thenDepositPaymentSessionShouldExist(
            $reservationId,
            null,
            6
        );
    }

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideDepositPaymentSessionCreationFailedData
     */
    public function testHandleWithNoReservationReadModel(string $reservationId): void
    {
        $this->expectException(ReservationNotFoundException::class);

        $this->givenDepositPaymentSessionCreationFailed(
            $reservationId,
            1
        );
        $this->whenTheDepositPaymentSessionCreationFailedProjectionRuns();
        $this->thenDepositPaymentSessionShouldNotExist(
            $reservationId,
            null,
            2
        );
    }

    /**
     * @param int[]             $unitIds
     * @param string[]          $unitNames
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideDepositPaymentSessionCreationFailedData
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
        $this->givenDepositPaymentSessionCreationFailed(
            $reservationId,
            1
        );
        $this->whenTheDepositPaymentSessionCreationFailedProjectionRuns();
        $this->thenDepositPaymentSessionShouldNotExist(
            $reservationId,
            null,
            2
        );
    }

    /**
     * @param int[]             $unitIds
     * @param string[]          $unitNames
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideDepositPaymentSessionCreationFailedData
     */
    public function testHandleWithNoDepositPaymentSessionAggregate(
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
        $this->expectException(DepositPaymentSessionNotFoundException::class);

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
        $this->givenDepositPaymentSessionCreationFailed(
            $reservationId,
            1
        );
        $this->whenTheDepositPaymentSessionCreationFailedProjectionRuns();
        $this->thenDepositPaymentSessionShouldNotExist(
            $reservationId,
            null,
            1
        );
        $this->thenDepositPaymentSessionShouldNotExist(
            $reservationId,
            null,
            2
        );
    }

    /**
     * @param int[]             $unitIds
     * @param string[]          $unitNames
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideDepositPaymentSessionCreationFailedData
     */
    public function testHandleWithMismatchingDepositPaymentSessionAggregate(
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
        $this->expectException(DepositPaymentSessionNotFoundException::class);

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

        $this->givenDepositPaymentSessionCreationFailed(
            $reservationId,
            1
        );
        $this->whenTheDepositPaymentSessionCreationFailedProjectionRuns();
        $this->thenDepositPaymentSessionShouldNotExist(
            $reservationId,
            null,
            1
        );
    }

    public function provideDepositPaymentSessionCreationFailedData(): Generator
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

        yield 'Reservation and Unit ReadModel data with single unit' => [
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

        yield 'Reservation and Unit ReadModel data with multiple units' => [
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
