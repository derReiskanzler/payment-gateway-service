<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\Prospect\ValueObject;

final class ProspectEmail
{
    public static function fromString(string $email): self
    {
        return new self($email);
    }

    private function __construct(private string $email)
    {
    }

    public function toString(): string
    {
        return $this->email;
    }
}
