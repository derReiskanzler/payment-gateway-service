<?php

declare(strict_types=1);

use Allmyhomes\Infrastructure\Config\StripeWebhookConfig;

return [
    StripeWebhookConfig::WEBHOOK_SECRET => env('STRIPE_WEBHOOK_SECRET', 'whsec_M04BLlNbh66fD7p1DKBaAWQ2M51n1Z2o'),
];
