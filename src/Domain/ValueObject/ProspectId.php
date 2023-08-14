<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class ProspectId
{
    public static function fromString(string $id): self
    {
        return new self($id);
    }

    private function __construct(private string $id)
    {
        Assert::uuid($id);
    }

    public function toString(): string
    {
        return $this->id;
    }
}
