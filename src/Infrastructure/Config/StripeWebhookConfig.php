<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Config;

final class StripeWebhookConfig
{
    public const WEBHOOK_SECRET = 'webhook_secret';

    public const STRIPE_SIGNATURE = 'Stripe-Signature';
}
