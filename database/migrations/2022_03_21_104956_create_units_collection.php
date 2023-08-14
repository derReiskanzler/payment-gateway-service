<?php

declare(strict_types=1);

use EventEngine\Persistence\MultiModelStore;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

final class CreateUnitsCollection extends Migration
{
    private const COLLECTION_NAME = 'units';

    public function up(): void
    {
        $store = app()->make(MultiModelStore::class);

        $store->addCollection(self::COLLECTION_NAME);
    }

    public function down(): void
    {
        Schema::dropIfExists(self::COLLECTION_NAME);
    }
}
