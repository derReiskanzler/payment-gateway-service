<?php

declare(strict_types=1);

use EventEngine\Persistence\MultiModelStore;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

final class CreateReservationsCollection extends Migration
{
    private const COLLECTION_NAME = 'reservations';

    public function up()
    {
        $store = app()->make(MultiModelStore::class);

        $store->addCollection(self::COLLECTION_NAME);
    }

    public function down()
    {
        Schema::dropIfExists(self::COLLECTION_NAME);
    }
}
