<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations;

interface DeprecationConfigurationInterface
{
    public function getDeprecation(): string|bool;

    public function getLink(): string;

    public function getSunset(): string;
}
