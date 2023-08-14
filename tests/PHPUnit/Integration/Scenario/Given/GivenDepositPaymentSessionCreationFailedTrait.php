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

trait GivenDepositPaymentSessionCreationFailedTrait
{
    /**
     * @throws BindingResolutionException
     */
    final protected function givenDepositPaymentSessionCreationFailed(
        string $id,
        int $errorCount
    ): void {
        /**
         * @var GenericEvent $event
         */
        $event = $this->depositPaymentSessionCreationFailed(
            $id,
            $errorCount,
        );
        /** @var EventStore $eventStore */
        $eventStore = $this->service(EventStore::class);
        $eventStore->appendTo(
            new StreamName(Stream::PAYMENT_GATEWAY_DEPOSIT_PAYMENT_SESSION_STREAM),
            new ArrayIterator([$event])
        );
    }

    abstract protected function service(string $class): mixed;

    abstract protected function faker(): Generator;

    private function depositPaymentSessionCreationFailed(
        string $id,
        int $errorCount
    ): DomainMessage {
        return GenericDomainMessage::fromArray([
            'uuid' => $this->faker()->uuid,
            'message_name' => 'PaymentGateway.DepositPaymentSessionCreationFailed',
            'payload' => [
                'reservation_id' => $id,
                'error_count' => $errorCount,
                'occurred_at' => '2020-07-16T16:00:00.000000',
                'created_at' => '2020-07-16T16:00:00.000000',
            ],
            'metadata' => [
                '_aggregate_id' => $id,
                '_aggregate_type' => 'PaymentGateway.DepositPaymentSessionCreationFailed',
                '_aggregate_version' => $this->nextAggregateVersion('PaymentGateway.DepositPaymentSessionCreationFailed', $id),
            ],
            'created_at' => new DateTimeImmutable(),
        ]);
    }
}
