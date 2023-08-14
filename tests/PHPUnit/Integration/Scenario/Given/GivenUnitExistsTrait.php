<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheUnitProjectionRunsTrait;

trait GivenUnitExistsTrait
{
    use GivenPlatformUnitContentPublishedTrait;
    use WhenTheUnitProjectionRunsTrait;

    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final public function givenUnitExists(int $unitId, string $unitName): void
    {
        $this->givenPlatformUnitContentPublished($unitId, $unitName);
        $this->whenTheUnitProjectionRuns();
    }
}
