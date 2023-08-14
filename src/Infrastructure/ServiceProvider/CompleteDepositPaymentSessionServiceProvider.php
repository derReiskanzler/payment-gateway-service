<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\ServiceProvider;

use Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Command\CompleteDepositPaymentSessionHandler;
use Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Command\CompleteDepositPaymentSessionHandlerInterface;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Providers\EventServiceProvider;

final class CompleteDepositPaymentSessionServiceProvider extends EventServiceProvider
{
    /**
     * Register.
     *
     * @codeCoverageIgnore
     */
    public function register(): void
    {
        $this->app->bind(CompleteDepositPaymentSessionHandlerInterface::class, CompleteDepositPaymentSessionHandler::class);
    }
}
