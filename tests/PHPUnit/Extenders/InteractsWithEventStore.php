<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Extenders;

use EventEngine\EventStore\EventStore;
use EventEngine\Persistence\MultiModelStore;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Extenders\Constraint\EventOnStream;

trait InteractsWithEventStore
{
    use InteractsWithLaravelContainer;

    protected function eventStore(): EventStore
    {
        return $this->service(MultiModelStore::class);
    }

    /**
     * Check that given event was recorded.
     *
     * @param string            $streamName    Stream Name
     * @param string            $aggregateType Aggregate Type
     * @param string            $aggregateId   Aggregate Id
     * @param string            $eventName     Event Name
     * @param array<mixed>|null $payload       Optional Payload to check
     * @param array<mixed>|null $metadata      Optional Metadata to check
     * @param int               $skip          Skip number of events with same name
     *
     * @throws BindingResolutionException
     *
     * @deprecated use InteractsWithEventStore::assertEventOnStream()
     */
    protected function eventRecordedInAggregateStream(
        string $streamName,
        string $aggregateType,
        string $aggregateId,
        string $eventName,
        ?array $payload = null,
        ?array $metadata = null,
        int $skip = 0
    ): void {
        $this->assertEventOnStream(
            $streamName,
            $aggregateType,
            $aggregateId,
            $eventName,
            $payload,
            $metadata,
            $skip,
        );
    }

    /**
     * Check that given event was recorded.
     *
     * @param string            $streamName    Stream Name
     * @param string            $aggregateType Aggregate Type
     * @param string            $aggregateId   Aggregate Id
     * @param string            $eventName     Event Name
     * @param array<mixed>|null $payload       Optional Payload to check
     * @param array<mixed>|null $metadata      Optional Metadata to check
     * @param int               $skip          Skip number of events with same name
     */
    final protected function assertEventOnStream(
        string $streamName,
        string $aggregateType,
        string $aggregateId,
        string $eventName,
        ?array $payload = null,
        ?array $metadata = null,
        int $skip = 0
    ): void {
        static::assertThat(
            $streamName,
            new EventOnStream(
                $this->eventStore(),
                $aggregateType,
                $aggregateId,
                $eventName,
                $payload,
                $metadata,
                $skip,
            )
        );
    }
}
