<?php

declare(strict_types=1);

namespace Tests\doubles;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Migrations\CollectionMigration;

class CollectionMigrationDouble extends CollectionMigration
{
    public string $collectionName = 'boilerplate.users';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createCollection($this->collectionName);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->deleteCollection($this->collectionName);
    }

    public function getCollectionName(): string
    {
        return $this->collectionName;
    }

    public function setCollectionName(string $collectionName): void
    {
        $this->collectionName = $collectionName;
    }
}
