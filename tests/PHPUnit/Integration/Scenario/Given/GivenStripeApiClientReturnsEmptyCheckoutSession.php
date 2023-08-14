<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Tests\PHPUnit\Doubles\StripeServiceDouble;

trait GivenStripeApiClientReturnsEmptyCheckoutSession
{
    final protected function givenStripeApiClientReturnsEmptyCheckoutSession(): void
    {
        $apiClient = new StripeServiceDouble(true);
        $this->app->extend(StripeServiceInterface::class, fn () => $apiClient);
    }
}
