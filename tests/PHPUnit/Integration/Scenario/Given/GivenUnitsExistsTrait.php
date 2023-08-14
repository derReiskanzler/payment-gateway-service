<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\PHPUnit\Integration\Scenario\When\WhenTheUnitProjectionRunsTrait;

trait GivenUnitsExistsTrait
{
    use GivenPlatformUnitContentPublishedTrait;
    use WhenTheUnitProjectionRunsTrait;

    /**
     * @param int[]    $unitIds
     * @param string[] $unitNames
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final public function givenUnitsExists(array $unitIds, array $unitNames): void
    {
        foreach ($unitIds as $key => $id) {
            $unitName = $unitNames[$key];
            $this->givenPlatformUnitContentPublished($id, $unitName);
            $this->whenTheUnitProjectionRuns();
        }
    }
}
