<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs;

interface ReadModel
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
