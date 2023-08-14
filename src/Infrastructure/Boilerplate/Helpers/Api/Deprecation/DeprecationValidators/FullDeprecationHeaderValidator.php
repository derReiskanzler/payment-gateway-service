<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationValidators;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations\DeprecationConfigurationInterface;
use DateTimeInterface;
use InvalidArgumentException;

class FullDeprecationHeaderValidator implements DeprecationHeaderValidatorInterface
{
    /**
     * @throws InvalidArgumentException if the provided deprecation in the $deprecatedConfiguration is neither a valid date nor a boolean true
     * @throws InvalidArgumentException if the deprecation in the $deprecatedConfiguration is set after the sunset date
     */
    public function validate(DeprecationConfigurationInterface $deprecationConfiguration): void
    {
        $this->validateDeprecation($deprecationConfiguration);
        $this->validateSunset($deprecationConfiguration);
    }

    /**
     * @throws InvalidArgumentException if the deprecation in the $deprecatedConfiguration is neither a valid date nor a boolean true
     */
    private function validateDeprecation(DeprecationConfigurationInterface $deprecationConfiguration): void
    {
        if (!$deprecationConfiguration->getDeprecation()) {
            throw new InvalidArgumentException('Deprecation has to be a valid date or true');
        }
    }

    /**
     * @throws InvalidArgumentException if the deprecation in the $deprecatedConfiguration is set after the sunset date
     */
    private function validateSunset(DeprecationConfigurationInterface $deprecationConfiguration): void
    {
        $sunset = date_create_immutable($deprecationConfiguration->getSunset());
        $deprecation = \is_string($deprecationConfiguration->getDeprecation()) ? date_create_immutable($deprecationConfiguration->getDeprecation()) : null;

        if ($deprecation instanceof DateTimeInterface && $sunset <= $deprecation) {
            throw new InvalidArgumentException('Sunset has to be after Deprecation');
        }
    }
}
