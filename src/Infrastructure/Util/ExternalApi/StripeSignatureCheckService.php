<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\ExternalApi;

use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

final class StripeSignatureCheckService implements StripeSignatureCheckServiceInterface
{
    public function __construct(
        private StripeWebhookConfig $config,
    ) {
    }

    public function hasValidSignature(string $jsonContent, string $signature, ): bool|string
    {
        try {
            Webhook::constructEvent(
                $jsonContent,
                $signature,
                $this->config->webhookSecret()->toString(),
            );

            return true;
        } catch (SignatureVerificationException $e) {
            return $e->getMessage();
        }
    }
}
