<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\When;

use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Allmyhomes\Infrastructure\Projection;
use Illuminate\Contracts\Container\BindingResolutionException;

trait WhenTheDepositPaymentSessionCreatedProjectionRunsTrait
{
    /**
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     */
    final protected function whenTheDepositPaymentSessionCreatedProjectionRuns(): void
    {
        $this->runProjections([Projection::SEND_DEPOSIT_PAYMENT_EMAIL_TO_PROSPECT_PROJECTION]);
    }

    abstract protected function runProjections(array $projections): void;
}
