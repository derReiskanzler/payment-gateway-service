<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\ValueObject\UnitDeposit;
use Allmyhomes\Domain\ValueObject\UnitId;
use Allmyhomes\Domain\ValueObject\UnitName;

final class Unit
{
    private const ID = 'id';
    private const DEPOSIT = 'deposit';
    private const NAME = 'name';

    public function __construct(
        private UnitId $id,
        private ?UnitDeposit $deposit,
        private ?UnitName $name,
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

    public function name(): ?UnitName
    {
        return $this->name;
    }

    /**
     * @param array<mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): self
    {
        $unitId = UnitId::fromInt($data['id']);
        $unitDeposit = isset($data['deposit']) ? UnitDeposit::fromFloat($data['deposit']) : null;
        $unitName = isset($data['name']) ? UnitName::fromString($data['name']) : null;

        return new self($unitId, $unitDeposit, $unitName);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::ID => $this->id->toInt(),
            self::DEPOSIT => $this->deposit?->toFloat(),
            self::NAME => $this->name?->toString(),
        ];
    }
}
