<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\ValueObject;

final class UnitIdCollection
{
    /**
     * @var int[]
     */
    private array $ids;

    /**
     * @param int[] $ids
     */
    public static function fromArray(array $ids): self
    {
        return new self($ids);
    }

    /**
     * @param int[] $ids
     */
    private function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return int[]
     */
    public function toArray(): array
    {
        return $this->ids;
    }
}
