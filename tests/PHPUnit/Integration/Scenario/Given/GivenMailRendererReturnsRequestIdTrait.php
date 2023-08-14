<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\Domain\DepositPaymentEmail\Mail\MailerInterface;
use Tests\PHPUnit\Doubles\MailerDouble;

trait GivenMailRendererReturnsRequestIdTrait
{
    final protected function givenMailRendererReturnsRequestId(string $requestId, bool $shouldFail = false): void
    {
        $mailer = new MailerDouble($requestId, $shouldFail);
        $this->app->extend(MailerInterface::class, fn () => $mailer);
    }
}
