<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\ServiceProvider;

use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command\SendDepositPaymentEmailToProspectHandler;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command\SendDepositPaymentEmailToProspectHandlerInterface;
use Allmyhomes\Domain\DepositPaymentEmail\Repository\DepositPaymentEmailRepositoryInterface;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Providers\EventServiceProvider;
use Allmyhomes\Infrastructure\Outbound\Repository\Persistence\DepositPaymentEmail\DepositPaymentEmailRepository;

final class DepositPaymentEmailServiceProvider extends EventServiceProvider
{
    /**
     * Register.
     *
     * @codeCoverageIgnore
     */
    public function register(): void
    {
        $this->app->bind(DepositPaymentEmailRepositoryInterface::class, DepositPaymentEmailRepository::class);
        $this->app->bind(SendDepositPaymentEmailToProspectHandlerInterface::class, SendDepositPaymentEmailToProspectHandler::class);
    }
}
