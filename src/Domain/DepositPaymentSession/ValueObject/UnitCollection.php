<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Exception\UnitsNotFoundException;
use Allmyhomes\Domain\Reservation\ValueObject\UnitCollection as ReservationReadModelUnitCollection;
use Allmyhomes\Domain\Unit\ValueObject\UnitCollection as UnitReadModelCollection;
use Allmyhomes\Domain\ValueObject\TotalUnitDeposit;
use Allmyhomes\Domain\ValueObject\UnitIdCollection;

final class UnitCollection
{
    /**
     * @var array<int, Unit>
     */
    private array $units = [];

    private function __construct(Unit ...$units)
    {
        foreach ($units as $unit) {
            $this->units[] = $unit;
        }
    }

    public static function fromReadModelUnitCollections(
        UnitReadModelCollection $unitCollection,
        ReservationReadModelUnitCollection $reservationUnitCollection
    ): self {
        $matchingUnitsArray = [];
        $notFoundInUnitReadModelCollection = [];

        foreach ($reservationUnitCollection->units() as $reservationUnit) {
            $reservationUnitId = $reservationUnit->id()->toInt();
            $matchingUnitReadModel = $unitCollection->findById($reservationUnitId);

            if (empty($matchingUnitReadModel)) {
                $notFoundInUnitReadModelCollection[] = $reservationUnitId;
            } else {
                $matchingUnitsArray[] = array_merge($reservationUnit->toArray(), $matchingUnitReadModel->toArray());
            }
        }

        if (!empty($notFoundInUnitReadModelCollection)) {
            throw UnitsNotFoundException::forUnitIds(UnitIdCollection::fromArray($notFoundInUnitReadModelCollection));
        }

        return self::fromArray($matchingUnitsArray);
    }

    /**
     * @param array<int, mixed> $items
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

    public function totalUnitDeposit(): TotalUnitDeposit
    {
        $totalUnitDeposit = 0.0;

        foreach ($this->units as $unit) {
            /* @var Unit $unit */
            $totalUnitDeposit += $unit->deposit()?->toFloat();
        }

        return TotalUnitDeposit::fromFloat($totalUnitDeposit);
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

    public function adapt(): LineItemCollection
    {
        $lineItemCollection = new LineItemCollection();

        foreach ($this->units() as $unit) {
            $lineItem = LineItem::fromValueObjects(
                Quantity::fromInt(1),
                PriceData::fromValueObjects(
                    Currency::fromString('eur'),
                    UnitAmount::fromFloat($unit->deposit()?->toFloat()),
                    ProductData::withValueObjects(
                        ProductName::fromString($unit->name()?->toString()),
                    ),
                )
            );
            $lineItemCollection = $lineItemCollection->add($lineItem);
        }

        return $lineItemCollection;
    }
}
