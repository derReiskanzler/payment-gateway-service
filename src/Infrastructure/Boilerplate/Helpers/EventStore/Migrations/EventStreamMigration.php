<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Migrations;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\ContextStreamName;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\InvalidStreamNameFormat;
use ArrayIterator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;

abstract class EventStreamMigration extends Migration
{
    public const STREAM_SUFFIX = 'stream';

    public const STREAM_COLUMN = 'stream_name';

    public const CATEGORY_COLUMN = 'category';

    private const EVENT_STREAMS = 'event_streams';
    protected bool $migrateOnEventStore = false;

    private EventStore $eventStore;

    private EventStore $sharedEventStore;

    /**
     * @throws BindingResolutionException if EventStore is not bound
     * @throws BindingResolutionException if nothing is not bound to the tag "SharedEventStore"
     */
    public function __construct()
    {
        $this->eventStore = app()->make(EventStore::class);
        $this->sharedEventStore = app()->make('SharedEventStore');
    }

    /**
     * @param array<string, mixed> $metadata
     */
    protected function createEventStream(string $streamName, ?string $category = null, array $metadata = []): void
    {
        $contextStreamName = ContextStreamName::fromString($streamName)->toString();
        if ($this->migrateOnEventStore) {
            $this->validateSharedEventStreamName($contextStreamName);
        }

        $this->migrateEventStreamLocally($contextStreamName, $category, $metadata);

        if ($this->migrateOnEventStore) {
            $this->migrateOnEventStore($streamName, $category, $metadata);
        }
    }

    /**
     * @param array<string, mixed> $metadata
     */
    protected function migrateOnEventStore(string $streamName, ?string $category = null, array $metadata = []): void
    {
        $contextStreamName = ContextStreamName::fromString($streamName)->toString();
        $this->validateSharedEventStreamName($contextStreamName);

        if (Schema::connection($this->getEventStoreConnection())->hasTable($contextStreamName)) {
            $this->sharedEventStore->delete($this->getStreamName($contextStreamName));
        }

        $this->sharedEventStore->create($this->getStream($contextStreamName, $metadata));
        $this->updateStreamCategory($this->getEventStoreConnection(), $contextStreamName, $category);
    }

    protected function deleteEventStream(string $streamName): void
    {
        if ($this->migrateOnEventStore) {
            $this->sharedEventStore->delete($this->getStreamName($streamName));
        }

        $this->eventStore->delete($this->getStreamName($streamName));
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function migrateEventStreamLocally(string $contextStreamName, ?string $category = null, array $metadata = []): void
    {
        $this->eventStore->create($this->getStream($contextStreamName, $metadata));
        $this->updateStreamCategory($this->getDefaultConnection(), $contextStreamName, $category);
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function getStream(string $streamName, array $metadata): Stream
    {
        return new Stream(
            $this->getStreamName($streamName),
            new ArrayIterator([]),
            $metadata
        );
    }

    private function getStreamName(string $streamName): StreamName
    {
        return new StreamName($streamName);
    }

    private function updateStreamCategory(string $connection, string $streamName, ?string $category = null): void
    {
        if ($category) {
            DB::connection($connection)->table(self::EVENT_STREAMS)->where(self::STREAM_COLUMN, $streamName)->update([
                self::CATEGORY_COLUMN => $category,
            ]);
        }
    }

    private function getDefaultConnection(): string
    {
        return config('database.default');
    }

    private function getEventStoreConnection(): string
    {
        return config('database.event_store_connection');
    }

    /**
     * @throws InvalidStreamNameFormat if $streamName is not in format service_name-stream_name-stream
     */
    private function validateSharedEventStreamName(string $streamName): void
    {
        if (1 !== preg_match(
            sprintf('/^[a-z_]+-[a-z_0-9]+-%s$/', static::STREAM_SUFFIX),
            $streamName
        )) {
            throw InvalidStreamNameFormat::sharedEventStreamMissingContext($streamName);
        }
    }
}
