<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;

final class StateCollectionName
{
    use toString;

    public static function fromString(string $collectionName): self
    {
        return new self($collectionName);
    }

    private function __construct(private string $collectionName)
    {
    }

    public function toString(): string
    {
        return $this->collectionName;
    }
}
