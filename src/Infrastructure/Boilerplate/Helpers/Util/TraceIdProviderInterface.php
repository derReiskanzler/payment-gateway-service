<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util;

interface TraceIdProviderInterface
{
    public function getTraceId(): string;
}
