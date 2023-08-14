<?php

declare(strict_types=1);

namespace Tests\doubles;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Migrations\EventStreamMigration;

class EventStreamMigrationDouble extends EventStreamMigration
{
    public string $eventStream = 'boilerplate-testing_service-stream';

    protected bool $migrateOnEventStore = true;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createEventStream($this->eventStream, 'test_category', ['name' => 'test']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->deleteEventStream($this->eventStream);
    }

    public function setEventStream(string $eventStream): void
    {
        $this->eventStream = $eventStream;
    }

    public function getEventStream(): string
    {
        return $this->eventStream;
    }

    public function setMigrateOnEventStore(bool $migrateOnEventStore): void
    {
        $this->migrateOnEventStore = $migrateOnEventStore;
    }
}
