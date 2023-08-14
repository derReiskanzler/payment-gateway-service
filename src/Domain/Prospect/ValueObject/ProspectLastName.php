<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\Prospect\ValueObject;

final class ProspectLastName
{
    public static function fromString(string $lastName): self
    {
        return new self($lastName);
    }

    private function __construct(private string $lastName)
    {
    }

    public function toString(): string
    {
        return $this->lastName;
    }
}
