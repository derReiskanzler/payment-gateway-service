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

trait GivenReservationAcceptedByInventoryTrait
{
    /**
     * @param int[] $unitIds
     *
     * @throws BindingResolutionException
     */
    final protected function givenReservationAcceptedByInventory(
        string $id,
        array $unitIds
    ): void {
        /**
         * @var GenericEvent $event
         */
        $event = $this->reservationAcceptedByInventoryMessage(
            $id,
            $unitIds,
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
     * @param int[] $unitIds
     */
    private function reservationAcceptedByInventoryMessage(
        string $id,
        array $unitIds
    ): DomainMessage {
        return GenericDomainMessage::fromArray([
            'uuid' => $this->faker()->uuid,
            'message_name' => 'ReservationManagement.ReservationAcceptedByInventory',
            'payload' => [
                'reservation_id' => $id,
                'unit_ids' => $unitIds,
            ],
            'metadata' => [
                '_aggregate_id' => $id,
                '_aggregate_type' => 'ReservationManagement.ReservationAcceptedByInventory',
                '_aggregate_version' => $this->nextAggregateVersion('ReservationManagement.ReservationAcceptedByInventory', $id),
            ],
            'created_at' => new DateTimeImmutable(),
        ]);
    }
}
