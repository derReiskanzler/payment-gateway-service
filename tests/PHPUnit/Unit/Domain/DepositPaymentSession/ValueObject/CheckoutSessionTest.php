<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSession;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Generator;
use PHPUnit\Framework\TestCase;

final class CheckoutSessionTest extends TestCase
{
    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideCheckoutSessionData
     */
    public function testFromArray(array $checkoutSessionDataArray): void
    {
        $checkoutSession = CheckoutSession::fromArray($checkoutSessionDataArray);

        $this->assertInstanceOf(
            CheckoutSession::class,
            $checkoutSession,
            'created checkout session from array does not match expected class: CheckoutSession.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideCheckoutSessionData
     */
    public function testToArray(array $checkoutSessionDataArray): void
    {
        $checkoutSession = CheckoutSession::fromArray($checkoutSessionDataArray);
        $expectedCheckoutSessionDataArray = $checkoutSessionDataArray;
        $expectedCheckoutSessionDataArray[CheckoutSession::EXPIRES_AT] = ExpiresAt::fromSeconds(
            $checkoutSessionDataArray[CheckoutSession::EXPIRES_AT]
        )->toString();

        $this->assertEquals(
            $expectedCheckoutSessionDataArray,
            $checkoutSession->toArray(),
            'created checkout session to array does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideCheckoutSessionData
     */
    public function testId(array $checkoutSessionDataArray): void
    {
        $checkoutSession = CheckoutSession::fromArray($checkoutSessionDataArray);

        self::assertEquals(
            $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID],
            $checkoutSession->id()?->toString(),
            'id of checkout session does not match expected id.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideCheckoutSessionData
     */
    public function testStatus(array $checkoutSessionDataArray): void
    {
        $checkoutSession = CheckoutSession::fromArray($checkoutSessionDataArray);

        self::assertEquals(
            $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_STATUS],
            $checkoutSession->status()?->toString(),
            'status of checkout session does not match expected status.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideCheckoutSessionData
     */
    public function testUrl(array $checkoutSessionDataArray): void
    {
        $checkoutSession = CheckoutSession::fromArray($checkoutSessionDataArray);

        self::assertEquals(
            $checkoutSessionDataArray[CheckoutSession::CHECKOUT_SESSION_URL],
            $checkoutSession->url()?->toString(),
            'url of checkout session does not match expected url.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideCheckoutSessionData
     */
    public function testCurrency(array $checkoutSessionDataArray): void
    {
        $checkoutSession = CheckoutSession::fromArray($checkoutSessionDataArray);

        self::assertEquals(
            $checkoutSessionDataArray[CheckoutSession::CURRENCY],
            $checkoutSession->currency()?->toString(),
            'currency of checkout session does not match expected currency.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideCheckoutSessionData
     */
    public function testCustomerId(array $checkoutSessionDataArray): void
    {
        $checkoutSession = CheckoutSession::fromArray($checkoutSessionDataArray);

        self::assertEquals(
            $checkoutSessionDataArray[CheckoutSession::CUSTOMER_ID],
            $checkoutSession->customerId()?->toString(),
            'customer id of checkout session does not match expected customer id.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideCheckoutSessionData
     */
    public function testExpiresAt(array $checkoutSessionDataArray): void
    {
        $checkoutSession = CheckoutSession::fromArray($checkoutSessionDataArray);

        self::assertEquals(
            $checkoutSessionDataArray[CheckoutSession::EXPIRES_AT],
            $checkoutSession->expiresAt()->toSeconds(),
            'expires at date of checkout session does not match expected expires at date.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideCheckoutSessionData
     */
    public function testPaymentIntentId(array $checkoutSessionDataArray): void
    {
        $checkoutSession = CheckoutSession::fromArray($checkoutSessionDataArray);

        self::assertEquals(
            $checkoutSessionDataArray[CheckoutSession::PAYMENT_INTENT_ID],
            $checkoutSession->paymentIntentId()?->toString(),
            'payment intent id of checkout session does not match expected payment intent id.'
        );
    }

    /**
     * @param array<string, mixed> $checkoutSessionDataArray
     * @dataProvider provideCheckoutSessionData
     */
    public function testPaymentStatus(array $checkoutSessionDataArray): void
    {
        $checkoutSession = CheckoutSession::fromArray($checkoutSessionDataArray);

        self::assertEquals(
            $checkoutSessionDataArray[CheckoutSession::PAYMENT_STATUS],
            $checkoutSession->paymentStatus()->toString(),
            'payment status of checkout session does not match expected payment status.'
        );
    }

    public function provideCheckoutSessionData(): Generator
    {
        yield 'CheckoutSession data' => [
            'checkout session data array' => [
                CheckoutSession::CHECKOUT_SESSION_ID => 'cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2',
                CheckoutSession::CHECKOUT_SESSION_STATUS => 'open',
                CheckoutSession::CHECKOUT_SESSION_URL => 'https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl/',
                CheckoutSession::CURRENCY => 'eur',
                CheckoutSession::CUSTOMER_ID => 'customer id',
                CheckoutSession::EXPIRES_AT => 1653996507,
                CheckoutSession::PAYMENT_INTENT_ID => 'pi_1Dr1jX2eZvKYlo2C6r0iT7PO',
                CheckoutSession::PAYMENT_STATUS => 'unpaid',
            ],
        ];
    }
}
