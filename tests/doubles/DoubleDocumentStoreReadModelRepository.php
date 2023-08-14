<?php

declare(strict_types=1);

namespace Tests\doubles;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStoreReadModelRepository;

final class DoubleDocumentStoreReadModelRepository extends DocumentStoreReadModelRepository
{
    private const COLLECTION_NAME = 'TestCollectionDbTable';

    public function collectionName(): string
    {
        return self::COLLECTION_NAME;
    }
}
