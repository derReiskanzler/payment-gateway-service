<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\Infrastructure\Util\ExternalApi\StripeSignatureCheckServiceInterface;
use Tests\PHPUnit\Doubles\StripeSignatureCheckServiceDouble;

trait GivenStripeSignatureCheckServiceValidatesSignature
{
    final protected function givenStripeSignatureCheckServiceValidatesSignature(
        bool $shouldFail = false,
    ): void {
        $service = new StripeSignatureCheckServiceDouble($shouldFail);
        $this->app->extend(StripeSignatureCheckServiceInterface::class, fn () => $service);
    }
}
