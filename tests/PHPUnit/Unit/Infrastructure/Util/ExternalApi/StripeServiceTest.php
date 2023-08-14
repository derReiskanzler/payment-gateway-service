<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Util\ExternalApi;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSession;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\DepositTransferDeadline;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Util\ExternalApi\StripeService;
use Allmyhomes\Infrastructure\Util\ExternalApi\StripeServiceConfig;
use Generator;
use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

final class StripeServiceTest extends TestCase
{
    /**
     * @var LoggerInterface&MockObject
     */
    private LoggerInterface $logger;

    private StripeServiceConfig $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->config = StripeServiceConfig::fromArray([
            StripeServiceConfig::API_KEY => 'sk_test_51L153aLHVthCw2smA7c2NqAfnvnpOzV03wSynsJXtDp97wodZqpkyDL2AOuW6ZeZLVVivEkUv0Oodbq4cSvMzl7400k13X30Ep',
            StripeServiceConfig::MODE => 'payment',
            StripeServiceConfig::SUCCESS_URL => 'https://google.com/?session_id={CHECKOUT_SESSION_ID}',
            StripeServiceConfig::CANCEL_URL => 'https://google.com/?session_id={CHECKOUT_SESSION_ID}',
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * @param array<string, mixed> $sessionDataArray
     * @param array<string, mixed> $createCheckoutSessionDataArray
     * @dataProvider provideStripeServiceData
     */
    public function testCreateCheckoutSession(array $sessionDataArray, array $createCheckoutSessionDataArray): void
    {
        $session = Mockery::mock('alias:'.Session::class);
        $session
            ->shouldReceive('create')
            ->once()
            ->andReturnSelf()
            ->andSet('id', $sessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID])
            ->andSet('status', $sessionDataArray[CheckoutSession::CHECKOUT_SESSION_STATUS])
            ->andSet('url', $sessionDataArray[CheckoutSession::CHECKOUT_SESSION_URL])
            ->andSet('currency', $sessionDataArray[CheckoutSession::CURRENCY])
            ->andSet('customer', $sessionDataArray[CheckoutSession::CUSTOMER_ID])
            ->andSet('expires_at', $sessionDataArray[CheckoutSession::EXPIRES_AT])
            ->andSet('payment_intent', $sessionDataArray[CheckoutSession::PAYMENT_INTENT_ID])
            ->andSet('payment_status', $sessionDataArray[CheckoutSession::PAYMENT_STATUS]);

        $apiClient = new StripeService($this->config, $this->logger);

        $checkoutSession = $apiClient->createCheckoutSession(
            ReservationId::fromString($createCheckoutSessionDataArray['reservation_id']),
            AgentId::fromString($createCheckoutSessionDataArray['agent_id']),
            ProjectId::fromInt($createCheckoutSessionDataArray['project_id']),
            ProspectId::fromString($createCheckoutSessionDataArray['prospect_id']),
            Language::fromString($createCheckoutSessionDataArray['language']),
            UnitCollection::fromArray($createCheckoutSessionDataArray['units']),
            DepositTransferDeadline::fromSeconds($createCheckoutSessionDataArray['deposit_transfer_deadline']),
        );

        $sessionDataArray[CheckoutSession::EXPIRES_AT] = ExpiresAt::fromSeconds($sessionDataArray[CheckoutSession::EXPIRES_AT])->toString();

        self::assertInstanceOf(
            CheckoutSession::class,
            $checkoutSession,
            'created checkout session does not match expected class: CheckoutSession.'
        );
        self::assertEquals(
            $sessionDataArray,
            $checkoutSession->toArray(),
            'created checkout session to array does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $sessionDataArray
     * @param array<string, mixed> $createCheckoutSessionDataArray
     * @dataProvider provideStripeServiceData
     */
    public function testCreateCheckoutSessionWithApiErrorException(array $sessionDataArray, array $createCheckoutSessionDataArray): void
    {
        $apiErrorException = $this->getMockForAbstractClass(ApiErrorException::class);

        $session = Mockery::mock('alias:'.Session::class);
        $session
            ->shouldReceive('create')
            ->once()
            ->andThrow($apiErrorException);

        $apiClient = new StripeService($this->config, $this->logger);

        $checkoutSession = $apiClient->createCheckoutSession(
            ReservationId::fromString($createCheckoutSessionDataArray['reservation_id']),
            AgentId::fromString($createCheckoutSessionDataArray['agent_id']),
            ProjectId::fromInt($createCheckoutSessionDataArray['project_id']),
            ProspectId::fromString($createCheckoutSessionDataArray['prospect_id']),
            Language::fromString($createCheckoutSessionDataArray['language']),
            UnitCollection::fromArray($createCheckoutSessionDataArray['units']),
            DepositTransferDeadline::fromSeconds($createCheckoutSessionDataArray['deposit_transfer_deadline']),
        );

        self::assertEquals(
            null,
            $checkoutSession,
            'created checkout session does not match expected null.'
        );
    }

    /**
     * @param array<string, mixed> $sessionDataArray
     * @param array<string, mixed> $createCheckoutSessionDataArray
     * @dataProvider provideStripeServiceData
     */
    public function testCreateCheckoutSessionWithEmptyCustomer(array $sessionDataArray, array $createCheckoutSessionDataArray): void
    {
        $session = Mockery::mock('alias:'.Session::class);
        $session
            ->shouldReceive('create')
            ->once()
            ->andReturnSelf()
            ->andSet('id', $sessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID])
            ->andSet('status', $sessionDataArray[CheckoutSession::CHECKOUT_SESSION_STATUS])
            ->andSet('url', $sessionDataArray[CheckoutSession::CHECKOUT_SESSION_URL])
            ->andSet('currency', $sessionDataArray[CheckoutSession::CURRENCY])
            ->andSet('customer', null)
            ->andSet('expires_at', $sessionDataArray[CheckoutSession::EXPIRES_AT])
            ->andSet('payment_intent', $sessionDataArray[CheckoutSession::PAYMENT_INTENT_ID])
            ->andSet('payment_status', $sessionDataArray[CheckoutSession::PAYMENT_STATUS]);

        $apiClient = new StripeService($this->config, $this->logger);

        $checkoutSession = $apiClient->createCheckoutSession(
            ReservationId::fromString($createCheckoutSessionDataArray['reservation_id']),
            AgentId::fromString($createCheckoutSessionDataArray['agent_id']),
            ProjectId::fromInt($createCheckoutSessionDataArray['project_id']),
            ProspectId::fromString($createCheckoutSessionDataArray['prospect_id']),
            Language::fromString($createCheckoutSessionDataArray['language']),
            UnitCollection::fromArray($createCheckoutSessionDataArray['units']),
            DepositTransferDeadline::fromSeconds($createCheckoutSessionDataArray['deposit_transfer_deadline']),
        );

        $sessionDataArray[CheckoutSession::EXPIRES_AT] = ExpiresAt::fromSeconds($sessionDataArray[CheckoutSession::EXPIRES_AT])->toString();
        $sessionDataArray[CheckoutSession::CUSTOMER_ID] = null;

        self::assertInstanceOf(
            CheckoutSession::class,
            $checkoutSession,
            'created checkout session does not match expected class: CheckoutSession.'
        );
        self::assertEquals(
            $sessionDataArray,
            $checkoutSession->toArray(),
            'created checkout session to array does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $sessionDataArray
     * @param array<string, mixed> $createCheckoutSessionDataArray
     * @dataProvider provideStripeServiceData
     */
    public function testCreateCheckoutSessionWithEmptyPaymentIntent(array $sessionDataArray, array $createCheckoutSessionDataArray): void
    {
        $session = Mockery::mock('alias:'.Session::class);
        $session
            ->shouldReceive('create')
            ->once()
            ->andReturnSelf()
            ->andSet('id', $sessionDataArray[CheckoutSession::CHECKOUT_SESSION_ID])
            ->andSet('status', $sessionDataArray[CheckoutSession::CHECKOUT_SESSION_STATUS])
            ->andSet('url', $sessionDataArray[CheckoutSession::CHECKOUT_SESSION_URL])
            ->andSet('currency', $sessionDataArray[CheckoutSession::CURRENCY])
            ->andSet('customer', $sessionDataArray[CheckoutSession::CUSTOMER_ID])
            ->andSet('expires_at', $sessionDataArray[CheckoutSession::EXPIRES_AT])
            ->andSet('payment_intent', null)
            ->andSet('payment_status', $sessionDataArray[CheckoutSession::PAYMENT_STATUS]);

        $apiClient = new StripeService($this->config, $this->logger);

        $checkoutSession = $apiClient->createCheckoutSession(
            ReservationId::fromString($createCheckoutSessionDataArray['reservation_id']),
            AgentId::fromString($createCheckoutSessionDataArray['agent_id']),
            ProjectId::fromInt($createCheckoutSessionDataArray['project_id']),
            ProspectId::fromString($createCheckoutSessionDataArray['prospect_id']),
            Language::fromString($createCheckoutSessionDataArray['language']),
            UnitCollection::fromArray($createCheckoutSessionDataArray['units']),
            DepositTransferDeadline::fromSeconds($createCheckoutSessionDataArray['deposit_transfer_deadline']),
        );

        $sessionDataArray[CheckoutSession::EXPIRES_AT] = ExpiresAt::fromSeconds($sessionDataArray[CheckoutSession::EXPIRES_AT])->toString();
        $sessionDataArray[CheckoutSession::PAYMENT_INTENT_ID] = null;

        self::assertInstanceOf(
            CheckoutSession::class,
            $checkoutSession,
            'created checkout session does not match expected class: CheckoutSession.'
        );
        self::assertEquals(
            $sessionDataArray,
            $checkoutSession->toArray(),
            'created checkout session to array does not match expected array.'
        );
    }

    public function provideStripeServiceData(): Generator
    {
        yield 'StripeService data' => [
            'session data array' => [
                CheckoutSession::CHECKOUT_SESSION_ID => 'cs_test_a1TpDn0YJ3sKoj7lbOq1DbWGrnGQMPT4Kdg837nN8pyFarROhHuvOp71BC',
                CheckoutSession::CHECKOUT_SESSION_STATUS => 'open',
                CheckoutSession::CHECKOUT_SESSION_URL => 'https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl',
                CheckoutSession::CURRENCY => 'eur',
                CheckoutSession::CUSTOMER_ID => 'customer id',
                CheckoutSession::EXPIRES_AT => 1653004800,
                CheckoutSession::PAYMENT_INTENT_ID => 'pi_3KSOXmJHRV8spf0Q1Vaclh9l',
                CheckoutSession::PAYMENT_STATUS => 'unpaid',
            ],
            'create checkout session data array' => [
                'units' => [
                    0 => [
                        'id' => 1,
                        'deposit' => 3000.00,
                        'name' => 'WE 1',
                    ],
                ],
                'language' => 'de',
                'agent_id' => 'da7c58f5-4c74-4722-8b94-7fcf8d857055',
                'project_id' => 80262,
                'prospect_id' => 'ca50819f-e5a4-40d3-a425-daba3e095407',
                'reservation_id' => '1234-1234-12345',
                'deposit_transfer_deadline' => 1653996507,
            ],
        ];
    }
}
