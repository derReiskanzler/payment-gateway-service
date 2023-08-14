<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\Reservation\ValueObject;

use Allmyhomes\Domain\ValueObject\UnitDeposit;
use Allmyhomes\Domain\ValueObject\UnitId;

final class Unit
{
    private const ID = 'id';
    private const DEPOSIT = 'deposit';

    public function __construct(
        private UnitId $id,
        private ?UnitDeposit $deposit,
    ) {
    }

    public function id(): UnitId
    {
        return $this->id;
    }

    public function deposit(): ?UnitDeposit
    {
        return $this->deposit;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): self
    {
        $unitId = UnitId::fromInt($data['id']);
        $unitDeposit = isset($data['deposit']) ? UnitDeposit::fromFloat($data['deposit']) : null;

        return new self($unitId, $unitDeposit);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::ID => $this->id->toInt(),
            self::DEPOSIT => $this->deposit()?->toFloat(),
        ];
    }
}
