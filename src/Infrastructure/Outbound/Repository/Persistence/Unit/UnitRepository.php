<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Outbound\Repository\Persistence\Unit;

use Allmyhomes\Application\UseCase\PopulateUnit\Document\Unit;
use Allmyhomes\Application\UseCase\PopulateUnit\Repository\UnitRepositoryInterface;
use Allmyhomes\Domain\Unit\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\UnitIdCollection;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStoreReadModelRepository;
use EventEngine\DocumentStore\Filter\AnyOfDocIdFilter;

final class UnitRepository extends DocumentStoreReadModelRepository implements UnitRepositoryInterface
{
    private const COLLECTION_NAME = 'units';

    public function upsert(Unit $unit): void
    {
        $this->upsertDocument((string) $unit->id(), $unit->toArray());
    }

    public function getByIds(UnitIdCollection $ids): ?UnitCollection
    {
        $units = $this->findDocuments(new AnyOfDocIdFilter($ids->toArray()));
        $unitsArray = iterator_to_array($units);

        if (empty($unitsArray)) {
            return null;
        }

        return UnitCollection::fromArray($unitsArray);
    }

    public function collectionName(): string
    {
        return self::COLLECTION_NAME;
    }
}
