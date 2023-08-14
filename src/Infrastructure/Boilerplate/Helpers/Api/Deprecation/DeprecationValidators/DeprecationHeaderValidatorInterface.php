<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationValidators;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations\DeprecationConfigurationInterface;

interface DeprecationHeaderValidatorInterface
{
    public function validate(DeprecationConfigurationInterface $deprecationConfiguration): void;
}
