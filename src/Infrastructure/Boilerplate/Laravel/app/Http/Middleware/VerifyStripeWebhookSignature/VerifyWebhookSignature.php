<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyStripeWebhookSignature;

use Allmyhomes\Infrastructure\Config\StripeWebhookConfig as WebhookConfig;
use Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Exception\WebhookJsonContentIsNotAStringException;
use Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Exception\WebhookSignatureIsNotAStringException;
use Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Exception\WebhookSignatureVerificationIsNotValidException;
use Allmyhomes\Infrastructure\Util\ExternalApi\StripeSignatureCheckServiceInterface;
use Closure;
use Illuminate\Http\Request;

class VerifyWebhookSignature
{
    public function __construct(
        private StripeSignatureCheckServiceInterface $stripeSignatureCheckService,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $jsonContent = $request->getContent();
        if (!is_string($jsonContent)) {
            throw new WebhookJsonContentIsNotAStringException();
        }

        $signature = $request->header(WebhookConfig::STRIPE_SIGNATURE);
        if (!is_string($signature)) {
            throw new WebhookSignatureIsNotAStringException();
        }

        $errorMessage = $this->stripeSignatureCheckService->hasValidSignature(
            $jsonContent,
            $signature,
        );

        if (is_string($errorMessage)) {
            throw WebhookSignatureVerificationIsNotValidException::forWebhookSignature($signature, $errorMessage);
        } else {
            return $next($request);
        }
    }
}
