<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Doubles;

use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSession;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;

final class StripeServiceDouble implements StripeServiceInterface
{
    public function __construct(
        private bool $shouldFail = false,
        private string $checkoutSessionId = 'cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2',
        private string $checkoutSessionStatus = 'open',
        private string $checkoutSessionUrl = 'https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl',
        private string $currency = 'eur',
        private string $customerId = 'customer id',
        private int $expiresAt = 1653004800,
        private string $paymentIntentId = 'pi_1Dr1jX2eZvKYlo2C6r0iT7PO',
        private string $paymentStatus = 'unpaid',
    ) {
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
        if ($this->shouldFail) {
            return null;
        }

        return CheckoutSession::fromArray([
            CheckoutSession::CHECKOUT_SESSION_ID => $this->checkoutSessionId,
            CheckoutSession::CHECKOUT_SESSION_STATUS => $this->checkoutSessionStatus,
            CheckoutSession::CHECKOUT_SESSION_URL => $this->checkoutSessionUrl,
            CheckoutSession::CURRENCY => $this->currency,
            CheckoutSession::CUSTOMER_ID => $this->customerId,
            CheckoutSession::EXPIRES_AT => $this->expiresAt,
            CheckoutSession::PAYMENT_INTENT_ID => $this->paymentIntentId,
            CheckoutSession::PAYMENT_STATUS => $this->paymentStatus,
        ]);
    }
}
