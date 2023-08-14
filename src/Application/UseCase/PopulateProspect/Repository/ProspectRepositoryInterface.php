<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\PopulateProspect\Repository;

use Allmyhomes\Application\UseCase\PopulateProspect\Document\Prospect;
use Allmyhomes\Domain\ValueObject\ProspectId;

interface ProspectRepositoryInterface
{
    public function upsert(Prospect $prospect): void;

    public function getById(ProspectId $prospectId): ?Prospect;

    public function delete(ProspectId $prospectId): void;
}
