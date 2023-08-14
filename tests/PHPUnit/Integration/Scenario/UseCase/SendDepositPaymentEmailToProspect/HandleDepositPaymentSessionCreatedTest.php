<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\UseCase\SendDepositPaymentEmailToProspect;

use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception\ProspectNotFoundException;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception\ReservationNotFoundException;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Exception\UnitsNotFoundException;
use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\Given\GivenDepositPaymentSessionCreatedTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenMailRendererReturnsRequestIdTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenProspectExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenReservationExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenUnitsExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenDepositPaymentEmailShouldExistTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenDepositPaymentEmailShouldNotExistTrait;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheDepositPaymentSessionCreatedProjectionRunsTrait;
use Tests\PHPUnit\IntegrationTestCase;

final class HandleDepositPaymentSessionCreatedTest extends IntegrationTestCase
{
    use GivenUnitsExistsTrait;
    use GivenReservationExistsTrait;
    use GivenProspectExistsTrait;
    use GivenDepositPaymentSessionCreatedTrait;
    use GivenMailRendererReturnsRequestIdTrait;
    use WhenTheDepositPaymentSessionCreatedProjectionRunsTrait;
    use ThenDepositPaymentEmailShouldExistTrait;
    use ThenDepositPaymentEmailShouldNotExistTrait;

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
        string $prospectId,
        string $checkoutSessionId,
        string $requestId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
        string $prospectEmail,
        string $prospectFirstName,
        string $prospectLastName,
        int $prospectSalutation,
    ): void {
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
        $this->givenProspectExists(
            $prospectId,
            $prospectEmail,
            $prospectFirstName,
            $prospectLastName,
            $prospectSalutation,
        );
        $this->givenUnitsExists($unitIds, $unitNames);
        $this->givenMailRendererReturnsRequestId($requestId);

        $this->givenDepositPaymentSessionCreated(
            $reservationId,
            $prospectId,
            $checkoutSessionId,
        );
        $this->whenTheDepositPaymentSessionCreatedProjectionRuns();
        $this->thenDepositPaymentEmailShouldExist(
            $reservationId,
            $prospectId,
            $checkoutSessionId,
            $requestId,
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
    public function testHandleWithMailRendererError(
        string $reservationId,
        string $prospectId,
        string $checkoutSessionId,
        string $requestId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
        string $prospectEmail,
        string $prospectFirstName,
        string $prospectLastName,
        int $prospectSalutation,
    ): void {
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
        $this->givenProspectExists(
            $prospectId,
            $prospectEmail,
            $prospectFirstName,
            $prospectLastName,
            $prospectSalutation,
        );
        $this->givenUnitsExists($unitIds, $unitNames);
        $this->givenMailRendererReturnsRequestId($requestId, true);

        $this->givenDepositPaymentSessionCreated(
            $reservationId,
            $prospectId,
            $checkoutSessionId,
        );
        $this->whenTheDepositPaymentSessionCreatedProjectionRuns();
        $this->thenDepositPaymentEmailShouldNotExist(
            $reservationId,
            $prospectId,
            $checkoutSessionId,
            $requestId,
        );
        $this->thenDepositPaymentEmailShouldExist(
            $reservationId,
            $prospectId,
            $checkoutSessionId,
            null,
            1
        );
    }

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideReservationAcceptedByInventoryData
     */
    public function testHandleWithNoReservationReadModel(
        string $reservationId,
        string $prospectId,
        string $checkoutSessionId,
        string $requestId,
    ): void {
        $this->expectException(ReservationNotFoundException::class);

        $this->givenMailRendererReturnsRequestId($requestId);
        $this->givenDepositPaymentSessionCreated(
            $reservationId,
            $prospectId,
            $checkoutSessionId,
        );
        $this->whenTheDepositPaymentSessionCreatedProjectionRuns();
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
    public function testHandleWithNoProspectReadModel(
        string $reservationId,
        string $prospectId,
        string $checkoutSessionId,
        string $requestId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
    ): void {
        $this->expectException(ProspectNotFoundException::class);

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

        $this->givenMailRendererReturnsRequestId($requestId);
        $this->givenDepositPaymentSessionCreated(
            $reservationId,
            $prospectId,
            $checkoutSessionId,
        );
        $this->whenTheDepositPaymentSessionCreatedProjectionRuns();
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
        string $prospectId,
        string $checkoutSessionId,
        string $requestId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
        string $prospectEmail,
        string $prospectFirstName,
        string $prospectLastName,
        int $prospectSalutation,
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

        $this->givenProspectExists(
            $prospectId,
            $prospectEmail,
            $prospectFirstName,
            $prospectLastName,
            $prospectSalutation,
        );

        $this->givenMailRendererReturnsRequestId($requestId);
        $this->givenDepositPaymentSessionCreated(
            $reservationId,
            $prospectId,
            $checkoutSessionId,
        );
        $this->whenTheDepositPaymentSessionCreatedProjectionRuns();
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
        $prospectEmail = 'max.mustermann@gmail.com';
        $prospectFirstName = 'Max';
        $prospectLastName = 'Mustermann';
        $prospectSalutation = 0;
        $requestId = 'request id';

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

        yield 'Deposit Payment Session Created Data with single unit' => [
            $reservationId,
            $prospectId,
            $checkoutSessionId,
            $requestId,
            $unitIds,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $unitNames,
            $units,
            $totalUnitDeposit,
            $prospectEmail,
            $prospectFirstName,
            $prospectLastName,
            $prospectSalutation,
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

        yield 'Deposit Payment Session Created Data with multiple units' => [
            $reservationId,
            $prospectId,
            $checkoutSessionId,
            $requestId,
            $unitIds,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $unitNames,
            $units,
            $totalUnitDeposit,
            $prospectEmail,
            $prospectFirstName,
            $prospectLastName,
            $prospectSalutation,
        ];
    }
}
