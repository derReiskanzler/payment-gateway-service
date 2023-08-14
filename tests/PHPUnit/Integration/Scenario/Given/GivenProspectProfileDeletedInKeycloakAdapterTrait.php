<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Prooph\Messaging\GenericDomainMessage;
use Allmyhomes\Infrastructure\Stream;
use ArrayIterator;
use DateTimeImmutable;
use EventEngine\Messaging\GenericEvent;
use Faker\Generator;
use Prooph\Common\Messaging\DomainMessage;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\StreamName;

trait GivenProspectProfileDeletedInKeycloakAdapterTrait
{
    final protected function givenProspectProfileDeletedInKeycloakAdapter(string $prospectId): void
    {
        /**
         * @var GenericEvent $event
         */
        $event = $this->prospectProfileDeletedInKeycloakAdapterMessage($prospectId);
        /** @var EventStore $eventStore */
        $eventStore = $this->service('SharedEventStore');
        $eventStore->appendTo(
            new StreamName(Stream::USER_USERS_STREAM),
            new ArrayIterator([$event])
        );
    }

    abstract protected function service(string $class): mixed;

    abstract protected function faker(): Generator;

    private function prospectProfileDeletedInKeycloakAdapterMessage(string $prospectId): DomainMessage
    {
        return GenericDomainMessage::fromArray([
            'uuid' => $this->faker()->uuid,
            'message_name' => 'User.ProspectProfileDeleted',
            'payload' => [
                'id' => $prospectId,
                'occurred_at' => '2020-06-27T21:37:45.531877',
            ],
            'metadata' => [
                '_aggregate_id' => $prospectId,
                '_aggregate_type' => 'User.ProspectProfileDeleted',
                '_aggregate_version' => $this->nextAggregateVersion('User.ProspectProfileDeleted', $prospectId),
            ],
            'created_at' => new DateTimeImmutable(),
        ]);
    }
}
