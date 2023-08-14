<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\When;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Allmyhomes\Infrastructure\Projection;
use Illuminate\Contracts\Container\BindingResolutionException;

trait WhenTheReservationAcceptedProjectionRunsTrait
{
    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final protected function whenTheReservationAcceptedProjectionRuns(): void
    {
        $this->runProjections([Projection::CREATE_DEPOSIT_PAYMENT_SESSION_PROJECTION]);
    }

    abstract protected function runProjections(array $projections): void;
}
