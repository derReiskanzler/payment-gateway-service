<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\PopulateReservation\Document;

use Allmyhomes\Application\UseCase\PopulateReservation\Document\Reservation;
use Generator;
use Tests\TestCase;

final class ReservationTest extends TestCase
{
    /**
     * @param array<string, mixed> $reservationData
     *
     * @dataProvider getReservationData
     */
    public function testId(array $reservationData): void
    {
        $reservation = Reservation::fromArray($reservationData);
        $this->assertEquals($reservationData['id'], $reservation->id()->toString(), 'id does not match expected id.');
    }

    /**
     * @param array<string, mixed> $reservationData
     *
     * @dataProvider getReservationData
     */
    public function testAgentId(array $reservationData): void
    {
        $reservation = Reservation::fromArray($reservationData);
        $this->assertEquals($reservationData['agent_id'], $reservation->agentId()->toString(), 'agent id does not match expected agent id.');
    }

    /**
     * @param array<string, mixed> $reservationData
     *
     * @dataProvider getReservationData
     */
    public function testDepositTransferDeadline(array $reservationData): void
    {
        $reservation = Reservation::fromArray($reservationData);
        $this->assertEquals(
            $reservationData['deposit_transfer_deadline'],
            $reservation->depositTransferDeadline()?->toString(),
            'deposit transfer deadline does not match expected deposit transfer deadline.'
        );
    }

    /**
     * @param array<string, mixed> $reservationData
     *
     * @dataProvider getReservationData
     */
    public function testLanguage(array $reservationData): void
    {
        $reservation = Reservation::fromArray($reservationData);
        $this->assertEquals($reservationData['language'], $reservation->language()->toString(), 'language does not match expected language.');
    }

    /**
     * @param array<string, mixed> $reservationData
     *
     * @dataProvider getReservationData
     */
    public function testProjectId(array $reservationData): void
    {
        $reservation = Reservation::fromArray($reservationData);
        $this->assertEquals($reservationData['project_id'], $reservation->projectId()->toInt(), 'project id does not match expected project id.');
    }

    /**
     * @param array<string, mixed> $reservationData
     *
     * @dataProvider getReservationData
     */
    public function testProspectId(array $reservationData): void
    {
        $reservation = Reservation::fromArray($reservationData);
        $this->assertEquals($reservationData['prospect_id'], $reservation->prospectId()->toString(), 'prospect id does not match expected prospect id.');
    }

    /**
     * @param array<string, mixed> $reservationData
     *
     * @dataProvider getReservationData
     */
    public function testTotalUnitDeposit(array $reservationData): void
    {
        $reservation = Reservation::fromArray($reservationData);

        $this->assertEquals(
            $reservationData['total_unit_deposit'],
            $reservation->totalUnitDeposit()->toFloat(),
            'total unit deposit does not match expected total unit deposit.'
        );
    }

    /**
     * @param array<string, mixed> $reservationData
     *
     * @dataProvider getReservationData
     */
    public function testUnits(array $reservationData): void
    {
        $reservation = Reservation::fromArray($reservationData);
        $this->assertEquals($reservationData['units'], $reservation->units()->toArray(), 'units do not match expected units.');
    }

    /**
     * @param array<string, mixed> $reservationData
     *
     * @dataProvider getReservationData
     */
    public function testToArray(array $reservationData): void
    {
        $this->assertEquals(
            $reservationData,
            Reservation::fromArray($reservationData)->toArray(),
            'reservation data does not match expected reservation data from toArray method',
        );
    }

    /**
     * @param array<string, mixed> $reservationData
     *
     * @dataProvider getReservationData
     */
    public function testFromArray(array $reservationData): void
    {
        $fromReservation = Reservation::fromArray($reservationData);

        $this->assertInstanceOf(
            Reservation::class,
            $fromReservation,
            'created reservation is not instance of expected class: Reservation.'
        );
    }

    /**
     * @return Generator<string, mixed>
     */
    public function getReservationData(): Generator
    {
        yield 'Reservation with full payload' => [
            'reservation_data' => [
                'id' => '1111-2222-3333',
                'agent_id' => 'da7c58f5-4c74-4722-8b94-7fcf8d857055',
                'deposit_transfer_deadline' => '2020-06-27T21:37:45.531877',
                'language' => 'de',
                'project_id' => 80262,
                'prospect_id' => 'ca50819f-e5a4-40d3-a425-daba3e095407',
                'total_unit_deposit' => 3000.00,
                'units' => [
                    0 => [
                        'id' => 1111,
                        'deposit' => 3000.0,
                    ],
                ],
            ],
        ];

        yield 'Reservation payload without optionals' => [
            'reservation_data' => [
                'id' => '1111-2222-3333',
                'agent_id' => 'da7c58f5-4c74-4722-8b94-7fcf8d857055',
                'deposit_transfer_deadline' => null,
                'language' => 'de',
                'project_id' => 80262,
                'prospect_id' => 'ca50819f-e5a4-40d3-a425-daba3e095407',
                'total_unit_deposit' => 3000.00,
                'units' => [
                    0 => [
                        'id' => 1111,
                        'deposit' => 3000.0,
                    ],
                ],
            ],
        ];
    }
}
