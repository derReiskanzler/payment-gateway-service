<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\System\Clock;

use DateTimeImmutable;

interface Clock
{
    public function now(): DateTimeImmutable;
}
