<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateProjectionsTable extends Migration
{
    private string $projection = 'projections';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->projection, function (Blueprint $table) {
            $this->getProjectionsBlueprint($table);
        });

        if (!Schema::connection($this->getEventStoreConnection())->hasTable($this->projection)) {
            Schema::connection($this->getEventStoreConnection())->create($this->projection, function (Blueprint $table) {
                $this->getProjectionsBlueprint($table);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->projection);
    }

    /**
     * @param Blueprint $table Blueprint StreamTable
     */
    private function getProjectionsBlueprint(Blueprint $table): Blueprint
    {
        $table->bigInteger('no')->autoIncrement()->unsigned();
        $table->string('name')->unique();
        $table->json('position')->nullable();
        $table->json('state')->nullable();
        $table->string('status');
        $table->string('locked_until')->nullable();
        $table->timestamp('created_at')->useCurrent();
        $table->timestamp('updated_at')->useCurrent();

        return $table;
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    private function getEventStoreConnection()
    {
        return config('database.event_store_connection');
    }
}
