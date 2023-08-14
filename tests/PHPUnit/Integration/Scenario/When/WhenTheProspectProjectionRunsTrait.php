<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\When;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Allmyhomes\Infrastructure\Projection;
use Illuminate\Contracts\Container\BindingResolutionException;

trait WhenTheProspectProjectionRunsTrait
{
    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final protected function whenTheProspectProjectionRuns(): void
    {
        $this->runProjections([Projection::PROSPECTS_PROJECTION]);
    }

    abstract protected function runProjections(array $projections): void;
}
