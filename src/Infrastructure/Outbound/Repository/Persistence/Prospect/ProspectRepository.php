<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Outbound\Repository\Persistence\Prospect;

use Allmyhomes\Application\UseCase\PopulateProspect\Document\Prospect;
use Allmyhomes\Application\UseCase\PopulateProspect\Repository\ProspectRepositoryInterface;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStoreReadModelRepository;

final class ProspectRepository extends DocumentStoreReadModelRepository implements ProspectRepositoryInterface
{
    private const COLLECTION_NAME = 'prospects';

    public function upsert(Prospect $prospect): void
    {
        $this->upsertDocument($prospect->id()->toString(), $prospect->toArray());
    }

    public function getById(ProspectId $prospectId): ?Prospect
    {
        $document = $this->getDocument($prospectId->toString());
        if (!$document) {
            return null;
        }

        return Prospect::fromArray($document);
    }

    public function delete(ProspectId $prospectId): void
    {
        $this->deleteDocument($prospectId->toString());
    }

    public function collectionName(): string
    {
        return self::COLLECTION_NAME;
    }
}
