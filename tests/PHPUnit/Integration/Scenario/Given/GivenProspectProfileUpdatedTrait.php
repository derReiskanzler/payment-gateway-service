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

trait GivenProspectProfileUpdatedTrait
{
    final protected function givenProspectProfileUpdated(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): void {
        /**
         * @var GenericEvent $event
         */
        $event = $this->prospectProfileUpdatedMessage(
            $prospectId,
            $email,
            $firstName,
            $lastName,
            $salutation,
        );
        /** @var EventStore $eventStore */
        $eventStore = $this->service('SharedEventStore');
        $eventStore->appendTo(
            new StreamName(Stream::USER_USERS_STREAM),
            new ArrayIterator([$event])
        );
    }

    abstract protected function service(string $class): mixed;

    abstract protected function faker(): Generator;

    private function prospectProfileUpdatedMessage(
        string $prospectId,
        string $email,
        string $firstName,
        string $lastName,
        int $salutation,
    ): DomainMessage {
        return GenericDomainMessage::fromArray([
            'uuid' => $this->faker()->uuid,
            'message_name' => 'User.ProspectProfileUpdated',
            'payload' => [
                'id' => $prospectId,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'salutation' => $salutation,
                'occurred_at' => '2020-06-27T21:37:45.531877',
            ],
            'metadata' => [
                '_aggregate_id' => $prospectId,
                '_aggregate_type' => 'User.ProspectProfileUpdated',
                '_aggregate_version' => $this->nextAggregateVersion('User.ProspectProfileUpdated', $prospectId),
            ],
            'created_at' => new DateTimeImmutable(),
        ]);
    }
}
