<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Prooph\Messaging\GenericDomainMessage;
use Allmyhomes\Infrastructure\Stream;
use ArrayIterator;
use DateTimeImmutable;
use EventEngine\Messaging\GenericEvent;
use Faker\Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Prooph\Common\Messaging\DomainMessage;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\StreamName;

trait GivenAgentInitiatedReservationTrait
{
    /**
     * @param array<int, mixed> $units
     *
     * @throws BindingResolutionException
     */
    final protected function givenAgentInitiatedReservation(
        string $id,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        float $totalUnitDeposit,
        array $units
    ): void {
        /**
         * @var GenericEvent $event
         */
        $event = $this->agentInitiatedReservationMessage(
            $id,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $totalUnitDeposit,
            $units
        );
        /** @var EventStore $eventStore */
        $eventStore = $this->service('SharedEventStore');
        $eventStore->appendTo(
            new StreamName(Stream::RESERVATION_MANAGEMENT_RESERVATION_STREAM),
            new ArrayIterator([$event])
        );
    }

    abstract protected function service(string $class): mixed;

    abstract protected function faker(): Generator;

    /**
     * @param array<int, mixed> $units
     */
    private function agentInitiatedReservationMessage(
        string $id,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        float $totalUnitDeposit,
        array $units
    ): DomainMessage {
        return GenericDomainMessage::fromArray([
            'uuid' => $this->faker()->uuid,
            'message_name' => 'ReservationManagement.AgentInitiatedReservation',
            'payload' => [
                'id' => $id,
                'agent_id' => $agentId,
                'prospect_id' => $prospectId,
                'project_id' => $projectId,
                'units' => $units,
                'total_deposit' => $totalUnitDeposit,
                'deposit_transfer_deadline' => $depositTransferDeadline,
                'total_unit_price' => 170000.00,
                'status' => 'INITIATED',
                'language' => $language,
                'occurred_at' => '2022-03-14T21:37:45.531877',
            ],
            'metadata' => [
                '_aggregate_id' => $id,
                '_aggregate_type' => 'ReservationManagement.AgentInitiatedReservation',
                '_aggregate_version' => $this->nextAggregateVersion('ReservationManagement.AgentInitiatedReservation', $id),
            ],
            'created_at' => new DateTimeImmutable(),
        ]);
    }
}
