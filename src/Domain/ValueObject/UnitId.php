<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class UnitId
{
    public static function fromInt(int $id): self
    {
        return new self($id);
    }

    private function __construct(private int $id)
    {
        Assert::greaterThan($id, 0);
    }

    public function toInt(): int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
