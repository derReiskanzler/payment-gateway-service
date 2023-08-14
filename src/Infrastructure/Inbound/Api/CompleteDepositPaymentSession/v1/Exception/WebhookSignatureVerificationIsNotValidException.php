<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Exception;

use LogicException;

final class WebhookSignatureVerificationIsNotValidException extends LogicException
{
    public static function forWebhookSignature(string $signature, string $errorMessage): self
    {
        return new self(
            sprintf('webhook signature: \'[%s]\'  is not valid. Error message: \'[%s]\'', $signature, $errorMessage),
            400
        );
    }
}
