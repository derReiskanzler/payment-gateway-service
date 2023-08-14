<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;

final class CommandName
{
    use toString;

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    private function __construct(private string $name)
    {
    }

    public function toString(): string
    {
        return $this->name;
    }
}
