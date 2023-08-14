<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Helpers\EventSourcing;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\EventTranslator;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer\EventPayloadTranslatorInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer\SnakeCaseEventPayloadTranslator;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event\EventEnvelope;
use DateTimeImmutable;
use EventEngine\Messaging\GenericEvent;
use EventEngine\Persistence\InMemoryConnection;
use EventEngine\Persistence\MultiModelStore;
use EventEngine\Prooph\V7\EventStore\InMemoryMultiModelStore;
use EventEngine\Util\MapIterator;
use Tests\doubles\EventSourcing\UserBehaviorDouble;
use Tests\doubles\EventSourcing\UserDouble;
use Tests\doubles\EventSourcing\UserRegisteredWithOccurredAtDouble;
use Tests\TestCase;

class AbstractAggregateRootTest extends TestCase
{
    private MultiModelStore $store;

    private string $streamName = 'boilerplate-user-stream';

    private string $userId = '52255543-248e-42ae-adbf-490d6ed8d855';

    private string $aggregateType = 'AppTest.User';

    private EventTranslator $eventTranslator;

    protected function setUp(): void
    {
        $this->createApplication();
        $this->store = InMemoryMultiModelStore::fromConnection(new InMemoryConnection());
        $this->store->createStream($this->streamName);
    }

    /**
     * @testdox Aggregate Could be reconstituted from History with and without occurred_at
     */
    public function testAggregateCanBeReconstitutedFromHistoryWithOneOccurredAt(): void
    {
        $this->setEventPayloadTranslator(new SnakeCaseEventPayloadTranslator());

        /** @var GenericEvent[] $genericEvents */
        $genericEvents = [
            GenericEvent::fromArray([
                'message_name' => UserRegisteredWithOccurredAtDouble::eventName(),
                'uuid' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                'payload' => [
                    'user_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42577',
                    'name' => 'Jane Doe',
                ],
                'metadata' => [
                    'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                    '_aggregate_type' => $this->aggregateType,
                    '_aggregate_id' => $this->userId,
                    '_aggregate_version' => 1,
                ],
                'created_at' => DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', '2020-06-06T10:00:05'),
            ]), GenericEvent::fromArray([
                'message_name' => UserRegisteredWithOccurredAtDouble::eventName(),
                'uuid' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                'payload' => [
                    'user_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42577',
                    'name' => 'Jane Doe',
                    'occurred_at' => '2020-06-06T10:00:05.192019',
                ],
                'metadata' => [
                    'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                    '_aggregate_type' => $this->aggregateType,
                    '_aggregate_id' => $this->userId,
                    '_aggregate_version' => 2,
                ],
                'created_at' => DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', '2020-06-06T10:00:05'),
            ]),
        ];

        $this->store->appendTo($this->streamName, ...$genericEvents);
        $pastEvents = $this->store->loadAggregateEvents(
            $this->streamName,
            $this->aggregateType,
            $this->userId
        );

        $user = UserBehaviorDouble::reconstituteFromHistory(new MapIterator(
            $pastEvents,
            fn (GenericEvent $event): EventEnvelope => $this->eventTranslator->translateFromGenericToDomain($event)
        ));

        /** @var UserDouble $state */
        $state = $user->state();
        static::assertSame('Jane Doe', $state->name()->toString());
    }

    /**
     * @testdox Aggregate Could be reconstituted from History without occurred_at at all
     */
    public function testAggregateCanBeReconstitutedFromHistoryWithoutOccurredAt(): void
    {
        $this->setEventPayloadTranslator(new SnakeCaseEventPayloadTranslator());

        /** @var GenericEvent[] $genericEvents */
        $genericEvents = [
            GenericEvent::fromArray([
                'message_name' => UserRegisteredWithOccurredAtDouble::eventName(),
                'uuid' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                'payload' => [
                    'user_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42577',
                    'name' => 'Jane Doe',
                ],
                'metadata' => [
                    'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                    '_aggregate_type' => $this->aggregateType,
                    '_aggregate_id' => $this->userId,
                    '_aggregate_version' => 1,
                ],
                'created_at' => DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', '2020-06-06T10:00:05'),
            ]), GenericEvent::fromArray([
                'message_name' => UserRegisteredWithOccurredAtDouble::eventName(),
                'uuid' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                'payload' => [
                    'user_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42577',
                    'name' => 'Jane Doe',
                ],
                'metadata' => [
                    'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                    '_aggregate_type' => $this->aggregateType,
                    '_aggregate_id' => $this->userId,
                    '_aggregate_version' => 2,
                ],
                'created_at' => DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', '2020-06-06T10:00:05'),
            ]),
        ];

        $this->store->appendTo($this->streamName, ...$genericEvents);
        $pastEvents = $this->store->loadAggregateEvents(
            $this->streamName,
            $this->aggregateType,
            $this->userId
        );

        $user = UserBehaviorDouble::reconstituteFromHistory(new MapIterator(
            $pastEvents,
            fn (GenericEvent $event): EventEnvelope => $this->eventTranslator->translateFromGenericToDomain($event)
        ));

        /** @var UserDouble $state */
        $state = $user->state();
        static::assertSame('Jane Doe', $state->name()->toString());
    }

    /**
     * @testdox Aggregate Could be reconstituted from History with snake case keys
     */
    public function testAggregateCanBeReconstitutedFromHistoryWithSnakeCase(): void
    {
        $this->setEventPayloadTranslator(new SnakeCaseEventPayloadTranslator());

        /** @var GenericEvent[] $genericEvents */
        $genericEvents = [
            GenericEvent::fromArray([
                'message_name' => UserRegisteredWithOccurredAtDouble::eventName(),
                'uuid' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                'payload' => [
                    'user_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42577',
                    'name' => 'Jane Doe',
                    'occurred_at' => '2020-06-06T10:00:05.192019',
                ],
                'metadata' => [
                    'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                    '_aggregate_type' => $this->aggregateType,
                    '_aggregate_id' => $this->userId,
                    '_aggregate_version' => 1,
                ],
                'created_at' => DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', '2020-06-06T10:00:05'),
            ]),
        ];

        $this->store->appendTo($this->streamName, ...$genericEvents);
        $pastEvents = $this->store->loadAggregateEvents(
            $this->streamName,
            $this->aggregateType,
            $this->userId
        );

        $user = UserBehaviorDouble::reconstituteFromHistory(new MapIterator(
            $pastEvents,
            fn (GenericEvent $event): EventEnvelope => $this->eventTranslator->translateFromGenericToDomain($event)
        ));

        /** @var UserDouble $state */
        $state = $user->state();
        static::assertSame('dc243dd9-cfae-4cfd-83df-0ca016a42577', $state->userId()->toString());
    }

    /**
     * @testdox Aggregate Could be reconstituted from History with camel case keys
     */
    public function testAggregateCanBeReconstitutedFromHistoryWithCamelCase(): void
    {
        $this->setEventPayloadTranslator(new SnakeCaseEventPayloadTranslator());

        /** @var GenericEvent[] $genericEvents */
        $genericEvents = [
            GenericEvent::fromArray([
                'message_name' => UserRegisteredWithOccurredAtDouble::eventName(),
                'uuid' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                'payload' => [
                    'userId' => 'dc243dd9-cfae-4cfd-83df-0ca016a42577',
                    'name' => 'Jane Doe',
                    'occurred_at' => '2020-06-06T10:00:05.192019',
                ],
                'metadata' => [
                    'event_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
                    '_aggregate_type' => $this->aggregateType,
                    '_aggregate_id' => $this->userId,
                    '_aggregate_version' => 1,
                ],
                'created_at' => DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', '2020-06-06T10:00:05'),
            ]),
        ];

        $this->store->appendTo($this->streamName, ...$genericEvents);
        $pastEvents = $this->store->loadAggregateEvents(
            $this->streamName,
            $this->aggregateType,
            $this->userId
        );

        $user = UserBehaviorDouble::reconstituteFromHistory(new MapIterator(
            $pastEvents,
            fn (GenericEvent $event): EventEnvelope => $this->eventTranslator->translateFromGenericToDomain($event)
        ));

        /** @var UserDouble $state */
        $state = $user->state();
        static::assertSame('dc243dd9-cfae-4cfd-83df-0ca016a42577', $state->userId()->toString());
    }

    private function setEventPayloadTranslator(EventPayloadTranslatorInterface $eventPayloadTranslatorInterface): void
    {
        app()->bind(EventPayloadTranslatorInterface::class, static fn ($app) => $eventPayloadTranslatorInterface);

        $this->eventTranslator = new EventTranslator([
            UserRegisteredWithOccurredAtDouble::eventName() => UserRegisteredWithOccurredAtDouble::class,
        ]);
    }
}
