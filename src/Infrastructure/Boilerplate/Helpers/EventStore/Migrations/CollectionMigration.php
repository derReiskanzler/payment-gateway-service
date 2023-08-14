<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Migrations;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\StateCollectionName;
use EventEngine\Persistence\MultiModelStore;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Migrations\Migration;

abstract class CollectionMigration extends Migration
{
    private MultiModelStore $eventStore;

    /**
     * @throws BindingResolutionException if MultiModelStore is not bound
     */
    public function __construct()
    {
        $this->eventStore = app()->make(MultiModelStore::class);
    }

    protected function createCollection(string $collectionName): void
    {
        $stateCollectionName = StateCollectionName::fromString($collectionName);
        $this->eventStore->addCollection($stateCollectionName->toString());
    }

    protected function deleteCollection(string $collectionName): void
    {
        $this->eventStore->dropCollection($collectionName);
    }
}
