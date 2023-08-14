<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\ServiceProvider;

use Allmyhomes\Domain\DepositPaymentEmail\Mail\MailerInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\ApiClient;
use Allmyhomes\Infrastructure\Config\DepositPaymentEmailConfig;
use Allmyhomes\Infrastructure\Util\Mail\EmailConfig;
use Allmyhomes\Infrastructure\Util\Mail\Helper\MailRendererHelper;
use Allmyhomes\Infrastructure\Util\Mail\Helper\MailRendererHelperInterface;
use Allmyhomes\Infrastructure\Util\Mail\Helper\MailTranslationHelper;
use Allmyhomes\Infrastructure\Util\Mail\Helper\MailTranslationHelperInterface;
use Allmyhomes\Infrastructure\Util\Mail\Mailer;
use Allmyhomes\MailRendererClient\Contracts\AmhClientInterface;
use Illuminate\Support\ServiceProvider;

final class MailRendererServiceProvider extends ServiceProvider
{
    /**
     * Register.
     *
     * @codeCoverageIgnore
     */
    public function register(): void
    {
        $this->app->bind(AmhClientInterface::class, ApiClient::class);
        $this->app->bind(MailRendererHelperInterface::class, MailRendererHelper::class);
        $this->app->bind(MailTranslationHelperInterface::class, MailTranslationHelper::class);
        $this->app->bind(MailerInterface::class, Mailer::class);
        $this->app->bind(EmailConfig::class, function () {
            $config = config('deposit-payment-email');

            return EmailConfig::fromArray([
                EmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_EMAIL => $config[DepositPaymentEmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_EMAIL],
                EmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_NAME => $config[DepositPaymentEmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_NAME],
                EmailConfig::DEPOSIT_PAYMENT_EMAIL_SUBJECTS => $config[DepositPaymentEmailConfig::DEPOSIT_PAYMENT_EMAIL_SUBJECTS],
            ]);
        });
    }
}
