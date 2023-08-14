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

trait GivenDepositPaymentSessionCreatedTrait
{
    final protected function givenDepositPaymentSessionCreated(
        string $reservationId,
        string $prospectId,
        string $checkoutSessionId,
    ): void {
        /**
         * @var GenericEvent $event
         */
        $event = $this->depositPaymentSessionCreatedMessage(
            $reservationId,
            $prospectId,
            $checkoutSessionId,
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

    private function depositPaymentSessionCreatedMessage(
        string $reservationId,
        string $prospectId,
        string $checkoutSessionId,
    ): DomainMessage {
        return GenericDomainMessage::fromArray([
            'uuid' => $this->faker()->uuid,
            'message_name' => 'PaymentGateway.DepositPaymentSessionCreated',
            'payload' => [
                'reservation_id' => $reservationId,
                'checkout_session_id' => $checkoutSessionId,
                'agent_id' => 'da7c58f5-4c74-4722-8b94-7fcf8d857055',
                'project_id' => 80262,
                'prospect_id' => $prospectId,
                'unit_ids' => [1, 2, 3],
                'total_unit_deposit' => 6000.00,
                'language' => 'de',
                'currency' => 'eur',
                'customer_id' => 'customer id',
                'payment_intent_id' => 'payment intent id',
                'payment_status' => 'unpaid',
                'checkout_session_status' => 'open',
                'checkout_session_url' => 'https://www.example.com',
                'expires_at' => '2020-06-27T21:37:45.531877',
                'created_at' => '2020-06-27T21:37:45.531877',
                'occurred_at' => '2020-06-27T21:37:45.531877',
            ],
            'metadata' => [
                '_aggregate_id' => $prospectId,
                '_aggregate_type' => 'PaymentGateway.DepositPaymentSessionCreated',
                '_aggregate_version' => $this->nextAggregateVersion('PaymentGateway.DepositPaymentSessionCreated', $prospectId),
            ],
            'created_at' => new DateTimeImmutable(),
        ]);
    }
}
