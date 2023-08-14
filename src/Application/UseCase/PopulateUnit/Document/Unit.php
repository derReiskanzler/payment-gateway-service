<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\PopulateUnit\Document;

use Allmyhomes\Domain\ValueObject\UnitId;
use Allmyhomes\Domain\ValueObject\UnitName;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\ReadModel;

final class Unit implements ReadModel
{
    private const ID = 'id';
    private const NAME = 'name';

    public function __construct(
        private UnitId $id,
        private ?UnitName $name,
    ) {
    }

    public function id(): UnitId
    {
        return $this->id;
    }

    public function name(): ?UnitName
    {
        return $this->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::ID => $this->id()->toInt(),
            self::NAME => $this->name()?->toString(),
        ];
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): Unit
    {
        return new Unit(
            UnitId::fromInt($data[self::ID]),
            $data[self::NAME] ? UnitName::fromString($data[self::NAME]) : null,
        );
    }
}
