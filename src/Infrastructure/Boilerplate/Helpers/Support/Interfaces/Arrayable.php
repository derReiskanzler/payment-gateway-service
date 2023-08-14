<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Support\Interfaces;

use Illuminate\Contracts\Support\Arrayable as IlluminateArrayable;

/**
 * @deprecated since boilerplate 4.2.0 and will be removed
 */
interface Arrayable extends IlluminateArrayable
{
    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
