<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Scenario\Given;

use Allmyhomes\Domain\DepositPaymentSession\ExternalApi\StripeServiceInterface;
use Tests\PHPUnit\Doubles\StripeServiceDouble;

trait GivenStripeApiClientReturnsCheckoutSession
{
    final protected function givenStripeApiClientReturnsCheckoutSession(
        string $checkoutSessionId,
    ): void {
        $apiClient = new StripeServiceDouble(
            false,
            $checkoutSessionId,
            'open',
            'https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl',
            'eur',
            'customer id',
            1653004800,
            'pi_1Dr1jX2eZvKYlo2C6r0iT7PO',
            'unpaid',
        );
        $this->app->extend(StripeServiceInterface::class, fn () => $apiClient);
    }
}
