<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\PopulateUnit\Repository;

use Allmyhomes\Application\UseCase\PopulateUnit\Document\Unit;
use Allmyhomes\Domain\Unit\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\UnitIdCollection;

interface UnitRepositoryInterface
{
    public function upsert(Unit $unit): void;

    public function getByIds(UnitIdCollection $ids): ?UnitCollection;
}
