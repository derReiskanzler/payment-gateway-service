<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateEventStreamsTable extends Migration
{
    private string $eventStream = 'event_streams';

    public function up(): void
    {
        Schema::create($this->eventStream, function (Blueprint $table): void {
            $this->getEventStreamBlueprint($table);
        });

        if (!Schema::connection($this->getEventStoreConnection())->hasTable($this->eventStream)) {
            Schema::connection($this->getEventStoreConnection())->create($this->eventStream, function (Blueprint $table): void {
                $this->getEventStreamBlueprint($table);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists($this->eventStream);
    }

    /**
     * @param Blueprint $table Blueprint StreamTable
     */
    private function getEventStreamBlueprint(Blueprint $table): void
    {
        $table->bigInteger('no')->autoIncrement()->unsigned();
        $table->string('real_stream_name')->unique();
        $table->string('stream_name')->unique();
        $table->json('metadata')->nullable();
        $table->string('category')->nullable();

        $table->index(['category']);
    }

    private function getEventStoreConnection(): string
    {
        return config('database.event_store_connection');
    }
}
