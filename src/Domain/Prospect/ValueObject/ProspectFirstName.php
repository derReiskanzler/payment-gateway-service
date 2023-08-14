<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\Prospect\ValueObject;

final class ProspectFirstName
{
    public static function fromString(string $firstName): self
    {
        return new self($firstName);
    }

    private function __construct(private string $firstName)
    {
    }

    public function toString(): string
    {
        return $this->firstName;
    }
}
