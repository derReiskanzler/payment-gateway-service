<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EventStorePostgresOccurredAtStreamMigration extends Migration implements OccurredAtStreamMigrationInterface
{
    protected string $streamName;

    public function addOccurredAt(): void
    {
        DB::connection($this->getEventStoreConnection())->statement($this->upStatement());
    }

    public function rollbackOccurredAt(): void
    {
        DB::connection($this->getEventStoreConnection())->statement($this->downStatement());
    }

    private function upStatement(): string
    {
        return sprintf(
            'update "%s" as stream set payload = payload::jsonb || jsonb_build_object(\'occurred_at\', created_at);',
            $this->streamName
        );
    }

    private function downStatement(): string
    {
        return sprintf('update "%s" as stream set payload = payload::jsonb - \'occurred_at\';', $this->streamName);
    }

    private function getEventStoreConnection(): string
    {
        return config('database.event_store_connection');
    }
}
