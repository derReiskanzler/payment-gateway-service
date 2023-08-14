<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\When;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Allmyhomes\Infrastructure\Projection;
use Illuminate\Contracts\Container\BindingResolutionException;

trait WhenTheUnitProjectionRunsTrait
{
    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final protected function whenTheUnitProjectionRuns(): void
    {
        $this->runProjections([Projection::UNITS_PROJECTION]);
    }

    abstract protected function runProjections(array $projections): void;
}
