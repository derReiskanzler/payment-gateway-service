<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\ExternalApi;

use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSession;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Config\DepositPaymentSessionConfig;
use Psr\Log\LoggerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

final class StripeService implements StripeServiceInterface
{
    public function __construct(
        private StripeServiceConfig $config,
        private LoggerInterface $logger,
    ) {
        Stripe::setApiKey($this->config->apiKey());
    }

    public function createCheckoutSession(
        ReservationId $reservationId,
        AgentId $agentId,
        ProjectId $projectId,
        ProspectId $prospectId,
        Language $language,
        UnitCollection $unitCollection,
        ?DepositTransferDeadline $depositTransferDeadline,
    ): ?CheckoutSession {
        try {
            $session = Session::create([
                DepositPaymentSessionConfig::LINE_ITEMS => $unitCollection->adapt()->toArrayAndInCents(),
                DepositPaymentSessionConfig::MODE => $this->config->mode(),
                DepositPaymentSessionConfig::SUCCESS_URL => $this->config->successUrl(),
                DepositPaymentSessionConfig::CANCEL_URL => $this->config->cancelUrl(),
                DepositPaymentSessionConfig::LOCALE => $language->toString(),
                DepositPaymentSessionConfig::EXPIRES_AT => $depositTransferDeadline?->toSeconds(),
                DepositPaymentSessionConfig::METADATA => [
                    DepositPaymentSessionConfig::AGENT_ID => $agentId->toString(),
                    DepositPaymentSessionConfig::PROJECT_ID => $projectId->toInt(),
                    DepositPaymentSessionConfig::PROSPECT_ID => $prospectId->toString(),
                    DepositPaymentSessionConfig::RESERVATION_ID => $reservationId->toString(),
                ],
            ]);
        } catch (ApiErrorException $e) {
            $this->logger->error($e->getMessage());

            return null;
        }

        return CheckoutSession::fromArray([
            CheckoutSession::CHECKOUT_SESSION_ID => $session->id,
            CheckoutSession::CHECKOUT_SESSION_STATUS => $session->status,
            CheckoutSession::CHECKOUT_SESSION_URL => $session->url,
            CheckoutSession::CURRENCY => $session->currency,
            CheckoutSession::CUSTOMER_ID => $this->getCustomerIdFromSession($session),
            CheckoutSession::EXPIRES_AT => $session->expires_at,
            CheckoutSession::PAYMENT_INTENT_ID => $this->getPaymentIntentIdFromSession($session),
            CheckoutSession::PAYMENT_STATUS => $session->payment_status,
        ]);
    }

    private function getCustomerIdFromSession(Session $session): ?string
    {
        if (is_string($session->customer)) {
            return $session->customer;
        }

        return null;
    }

    private function getPaymentIntentIdFromSession(Session $session): ?string
    {
        if (is_string($session->payment_intent)) {
            return $session->payment_intent;
        }

        return null;
    }
}
