<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\ServiceProvider;

use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Providers\EventServiceProvider;
use Allmyhomes\Infrastructure\Config\DepositPaymentSessionConfig;
use Allmyhomes\Infrastructure\Util\ExternalApi\StripeService;
use Allmyhomes\Infrastructure\Util\ExternalApi\StripeServiceConfig;
use Allmyhomes\Infrastructure\Util\ExternalApi\StripeSignatureCheckService;
use Allmyhomes\Infrastructure\Util\ExternalApi\StripeSignatureCheckServiceInterface;
use Allmyhomes\Infrastructure\Util\ExternalApi\StripeWebhookConfig;

final class PaymentGatewayServiceProvider extends EventServiceProvider
{
    /**
     * Register.
     *
     * @codeCoverageIgnore
     */
    public function register(): void
    {
        $this->app->bind(StripeServiceInterface::class, StripeService::class);
        $this->app->bind(StripeServiceConfig::class, function () {
            $config = config('stripe');

            return StripeServiceConfig::fromArray([
                StripeServiceConfig::API_KEY => $config[DepositPaymentSessionConfig::API_KEY],
                StripeServiceConfig::MODE => $config[DepositPaymentSessionConfig::MODE],
                StripeServiceConfig::SUCCESS_URL => $config[DepositPaymentSessionConfig::SUCCESS_URL],
                StripeServiceConfig::CANCEL_URL => $config[DepositPaymentSessionConfig::CANCEL_URL],
            ]);
        });

        $this->app->bind(StripeSignatureCheckServiceInterface::class, StripeSignatureCheckService::class);
        $this->app->bind(StripeWebhookConfig::class, function () {
            $config = config('stripe-webhook');

            return StripeWebhookConfig::fromArray([
                StripeWebhookConfig::WEBHOOK_SECRET => $config[StripeWebhookConfig::WEBHOOK_SECRET],
            ]);
        });
    }
}
