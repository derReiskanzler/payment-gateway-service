<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\ContextStreamName;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\StateCollectionName;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\AggregateNotFound;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\ImmutableState;
use DateTimeImmutable;
use EventEngine\Messaging\GenericEvent;
use EventEngine\Persistence\InMemoryConnection;
use EventEngine\Persistence\MultiModelStore;
use EventEngine\Prooph\V7\EventStore\InMemoryMultiModelStore;
use Exception;
use Ramsey\Uuid\Uuid;
use Tests\doubles\CommandDouble;
use Tests\doubles\EventSourcing\UserBehaviorDouble;
use Tests\doubles\EventSourcing\UserDouble;
use Tests\doubles\EventSourcing\UserIdDouble;
use Tests\doubles\EventSourcing\UserNameDouble;
use Tests\doubles\EventSourcing\UserRegisteredDouble;
use Tests\doubles\EventSourcing\UserRenamedDouble;
use Tests\doubles\EventSourcing\UserRepositoryDouble;
use Tests\TestCase;

class MultiModelStoreAggregateRepositoryTest extends TestCase
{
    private const EVENT_STREAM = 'users-stream';
    private const STATE_COLLECTION = 'users';

    private UserRepositoryDouble $repository;

    private MultiModelStore $store;

    protected function setUp(): void
    {
        parent::setUp();

        $streamName = ContextStreamName::fromString(self::EVENT_STREAM);
        $stateCollectionName = StateCollectionName::fromString(self::STATE_COLLECTION);

        $this->store = InMemoryMultiModelStore::fromConnection(new InMemoryConnection());
        $this->store->createStream($streamName->toString());
        $this->store->addCollection($stateCollectionName->toString());

        $this->repository = new UserRepositoryDouble($this->store);
    }

    /**
     * @testdox Load list of events for specific aggregate until specific version
     */
    public function testLoadAggregateEventsUntilVersion(): void
    {
        $userId = UserIdDouble::generate();
        $this->addUserAndRename($userId);
        $this->store->deleteDoc(self::STATE_COLLECTION, $userId->toString());

        $aggregate = $this->repository->getUntilVersion($userId, 1);

        /** @var UserDouble $aggregateState */
        $aggregateState = $aggregate->state();
        static::assertSame('Jane Doe', $aggregateState->name()->toString());

        $aggregate = $this->repository->getUntilVersion($userId, 2);

        /** @var UserDouble $aggregateState */
        $aggregateState = $aggregate->state();
        static::assertSame('Rename Jane Doe', $aggregateState->name()->toString());
    }

    /**
     * @testdox Load list of events for specific aggregate for version than latest version
     */
    public function testLoadAggregateEventsThanLatestVersion(): void
    {
        $userId = UserIdDouble::generate();
        $this->addUserAndRename($userId);
        $this->store->deleteDoc(self::STATE_COLLECTION, $userId->toString());

        $aggregate = $this->repository->getUntilVersion($userId, 10);

        /** @var UserDouble $aggregateState */
        $aggregateState = $aggregate->state();
        static::assertSame('Rename Jane Doe', $aggregateState->name()->toString());
    }

    /**
     * @testdox Load list of events for non existing aggregate until specific version will throw exception
     */
    public function testLoadAggregateEventsUntilVersionThrowsException(): void
    {
        $userId = UserIdDouble::generate();

        $this->expectException(AggregateNotFound::class);

        $this->repository->getUntilVersion($userId, 1);
    }

    /**
     * @testdox Test Persisted Document Payload in Snakecase format by default
     */
    public function testPersistedSnakeCaseInDocumentPayload(): void
    {
        $userId = UserIdDouble::generate();
        $userName = UserNameDouble::fromString('Jane Doe');
        $aggregate = UserBehaviorDouble::register($userId, $userName);
        $command = new CommandDouble([]);
        $this->repository->save($aggregate, $command);
        $document = $this->store->getDoc(self::STATE_COLLECTION, $userId->toString());

        static::assertSame([
            'state' => [
                'user_id' => $userId->toString(),
                'name' => 'Jane Doe',
                'deleted' => false,
            ],
            'version' => 1,
            'metadata' => [
                'name' => 'Jane Doe',
            ],
        ], $document);
    }

    /**
     * @testdox Load Aggregate from SnakeCase Persisted State
     */
    public function testLoadAggregateStateFromSnakeCase(): void
    {
        $userId = UserIdDouble::generate();
        $this->store->addDoc(
            self::STATE_COLLECTION,
            $userId->toString(),
            [
                'state' => [
                    'user_id' => $userId->toString(),
                    'name' => 'Jane Doe',
                    'deleted' => false,
                ],
                'version' => 1,
                'metadata' => [
                    'name' => 'Jane Doe',
                ],
            ]
        );

        $aggregate = $this->repository->get($userId);

        /** @var ImmutableState $aggregateState */
        $aggregateState = $aggregate->state();
        static::assertSame([
            'userId' => $userId->toString(),
            'name' => 'Jane Doe',
            'deleted' => false,
        ], $aggregateState->toArray());
    }

    /**
     * @testdox Load Aggregate from CamelCase Persisted State
     */
    public function testLoadAggregateStateFromCamelCase(): void
    {
        $userId = UserIdDouble::generate();
        $this->store->addDoc(
            self::STATE_COLLECTION,
            $userId->toString(),
            [
                'state' => [
                    'userId' => $userId->toString(),
                    'name' => 'Jane Doe',
                    'deleted' => false,
                ],
                'version' => 1,
                'metadata' => [
                    'name' => 'Jane Doe',
                ],
            ]
        );

        $aggregate = $this->repository->get($userId);

        /** @var ImmutableState $aggregateState */
        $aggregateState = $aggregate->state();
        static::assertSame([
            'userId' => $userId->toString(),
            'name' => 'Jane Doe',
            'deleted' => false,
        ], $aggregateState->toArray());
    }

    private function addUserAndRename(UserIdDouble $userId): void
    {
        $userName = UserNameDouble::fromString('Jane Doe');

        $userRegistered = UserRegisteredDouble::fromRecordData([
            UserRegisteredDouble::USER_ID => $userId,
            UserRegisteredDouble::NAME => $userName,
        ]);
        $userRenamed = UserRenamedDouble::fromRecordData([
            UserRenamedDouble::USER_ID => $userId,
            UserRenamedDouble::NEW_NAME => UserNameDouble::fromString('Rename Jane Doe'),
        ]);
        $this->appendToEventStore(
            UserRegisteredDouble::eventName(),
            [
                '_aggregate_id' => $userId->toString(),
                '_aggregate_version' => 1,
                '_aggregate_type' => 'AppUnitTest.User',
            ],
            $userRegistered->toArray()
        );
        $this->appendToEventStore(
            UserRenamedDouble::eventName(),
            [
                '_aggregate_id' => $userId->toString(),
                '_aggregate_version' => 2,
                '_aggregate_type' => 'AppUnitTest.User',
            ],
            $userRenamed->toArray()
        );
    }

    /**
     * @param array<string, int|string> $metadata
     * @param array<string, int|string> $payload
     *
     * @throws Exception
     */
    private function appendToEventStore(string $eventName, array $metadata, array $payload): void
    {
        $this->store->appendTo(self::EVENT_STREAM, GenericEvent::fromArray([
            'uuid' => Uuid::uuid4()->toString(),
            'message_name' => $eventName,
            'metadata' => $metadata,
            'payload' => $payload,
            'created_at' => new DateTimeImmutable(),
        ]));
    }
}
