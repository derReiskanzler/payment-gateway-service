<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\Unit\ValueObject;

use Allmyhomes\Application\UseCase\PopulateUnit\Document\Unit;
use Allmyhomes\Domain\ValueObject\UnitIdCollection;

final class UnitCollection
{
    /**
     * @var Unit[]
     */
    private array $units = [];

    private function __construct(Unit ...$units)
    {
        foreach ($units as $unit) {
            $this->units[] = $unit;
        }
    }

    /**
     * @param array<int|string, mixed> $items
     */
    public static function fromArray(array $items): self
    {
        return new self(
            ...array_map(
                static function (array $item) {
                    return Unit::fromArray($item);
                },
                array_values($items)
            )
        );
    }

    public static function fromUnits(Unit ...$units): self
    {
        return new self(
            ...$units
        );
    }

    /**
     * @return Unit[]
     */
    public function units(): array
    {
        return $this->units;
    }

    /**
     * @return array<int, mixed>
     */
    public function toArray(): array
    {
        return array_map(
            function (Unit $unit) {
                return $unit->toArray();
            },
            $this->units
        );
    }

    public function add(Unit $unit): self
    {
        $copy = clone $this;
        $copy->units[] = $unit;

        return $copy;
    }

    public function count(): int
    {
        return count($this->units);
    }

    /**
     * @return int[]
     */
    public function ids(): array
    {
        return array_map(fn (Unit $unit) => $unit->id()->toInt(), $this->units);
    }

    public function idCollection(): UnitIdCollection
    {
        return UnitIdCollection::fromArray($this->ids());
    }

    public function findById(int $id): ?Unit
    {
        foreach ($this->units() as $unit) {
            if ($unit->id()->toInt() === $id) {
                return $unit;
            }
        }

        return null;
    }
}
