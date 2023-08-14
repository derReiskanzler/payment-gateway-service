<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\ServiceProvider;

use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command\CreateDepositPaymentSessionHandler;
use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command\CreateDepositPaymentSessionHandlerInterface;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command\RetryDepositPaymentSessionCreationHandler;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command\RetryDepositPaymentSessionCreationHandlerInterface;
use Allmyhomes\Domain\DepositPaymentSession\Repository\DepositPaymentSessionRepositoryInterface;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Providers\EventServiceProvider;
use Allmyhomes\Infrastructure\Outbound\Repository\Persistence\DepositPaymentSession\DepositPaymentSessionRepository;

final class DepositPaymentSessionServiceProvider extends EventServiceProvider
{
    /**
     * Register.
     *
     * @codeCoverageIgnore
     */
    public function register(): void
    {
        $this->app->bind(DepositPaymentSessionRepositoryInterface::class, DepositPaymentSessionRepository::class);
        $this->app->bind(CreateDepositPaymentSessionHandlerInterface::class, CreateDepositPaymentSessionHandler::class);
        $this->app->bind(RetryDepositPaymentSessionCreationHandlerInterface::class, RetryDepositPaymentSessionCreationHandler::class);
    }
}
