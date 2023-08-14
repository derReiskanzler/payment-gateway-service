<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\When;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Allmyhomes\Infrastructure\Projection;
use Illuminate\Contracts\Container\BindingResolutionException;

trait WhenTheDepositPaymentSessionCreationFailedProjectionRunsTrait
{
    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final protected function whenTheDepositPaymentSessionCreationFailedProjectionRuns(): void
    {
        $this->runProjections([Projection::RETRY_DEPOSIT_PAYMENT_SESSION_PROJECTION]);
    }

    abstract protected function runProjections(array $projections): void;
}
