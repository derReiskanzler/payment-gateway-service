<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Api\CompleteDepositPaymentSession\v1;

use Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Exception\DepositPaymentSessionNotFoundException;
use Allmyhomes\EventProjections\Exceptions\Configurations\ProjectionConfigurationInvalidException;
use Allmyhomes\Infrastructure\Config\StripeWebhookConfig;
use Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Exception\WebhookSignatureIsNotAStringException;
use Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Exception\WebhookSignatureVerificationIsNotValidException;
use Generator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Mockery;
use Tests\PHPUnit\Api\ApiTestCase;
use Tests\PHPUnit\Integration\Scenario\Given\GivenDepositPaymentSessionExistsTrait;
use Tests\PHPUnit\Integration\Scenario\Given\GivenStripeSignatureCheckServiceValidatesSignature;
use Tests\PHPUnit\Integration\Scenario\Then\ThenDepositPaymentSessionShouldExistTrait;
use Tests\PHPUnit\Integration\Scenario\Then\ThenDepositPaymentSessionShouldNotExistTrait;

final class CompleteDepositPaymentSessionTest extends ApiTestCase
{
    use GivenDepositPaymentSessionExistsTrait;
    use GivenStripeSignatureCheckServiceValidatesSignature;
    use ThenDepositPaymentSessionShouldExistTrait;
    use ThenDepositPaymentSessionShouldNotExistTrait;

    private const URL = 'v1/complete-deposit-payment-session';
    private const SIGNATURE = 't=1656064274,v1=6023147645a0bc886dabacee259d274f4fdfe8522848e78772a48934ede11294,v0=5e5bd208045aef5e3303dcee4f2ddc94f9f986d69ba37a973111057e5026cd8d';
    private const INVALID_SIGNATURE = [];

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * @param int[]                $unitIds
     * @param string[]             $unitNames
     * @param array<int, mixed>    $units
     * @param array<string, mixed> $webhookPayload
     *
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideCompleteDepositPaymentSessionData
     */
    public function testCompleteDepositPaymentSession(
        string $reservationId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
        string $checkoutSessionId,
        array $webhookPayload,
    ): void {
        $this->givenStripeSignatureCheckServiceValidatesSignature();
        $this->givenDepositPaymentSessionExists(
            $reservationId,
            $unitIds,
            $unitNames,
            $checkoutSessionId,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $totalUnitDeposit,
            $units,
        );

        $response = $this->postJson(
            self::URL,
            $webhookPayload,
            [
                StripeWebhookConfig::STRIPE_SIGNATURE => self::SIGNATURE,
            ],
        );

        $response->assertStatus(204);

        $this->thenDepositPaymentSessionShouldExist(
            $reservationId,
            $checkoutSessionId,
            0,
            'complete',
            'paid'
        );
    }

    /**
     * @param int[]                $unitIds
     * @param string[]             $unitNames
     * @param array<int, mixed>    $units
     * @param array<string, mixed> $webhookPayload
     *
     * @throws WebhookSignatureIsNotAStringException
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideCompleteDepositPaymentSessionData
     */
    public function testCompleteDepositPaymentSessionWithWebhookSignatureIsNotAStringException(
        string $reservationId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
        string $checkoutSessionId,
        array $webhookPayload
    ): void {
        $this->givenDepositPaymentSessionExists(
            $reservationId,
            $unitIds,
            $unitNames,
            $checkoutSessionId,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $totalUnitDeposit,
            $units,
        );

        $response = $this->postJson(
            self::URL,
            $webhookPayload,
            [
                StripeWebhookConfig::STRIPE_SIGNATURE => self::INVALID_SIGNATURE,
            ],
        );

        $response->assertStatus(400);

        $this->thenDepositPaymentSessionShouldExist(
            $reservationId,
            $checkoutSessionId,
            0,
            'open',
            'unpaid',
        );
    }

    /**
     * @param int[]                $unitIds
     * @param string[]             $unitNames
     * @param array<int, mixed>    $units
     * @param array<string, mixed> $webhookPayload
     *
     * @throws WebhookSignatureVerificationIsNotValidException
     * @throws BindingResolutionException
     * @throws ProjectionConfigurationInvalidException
     * @dataProvider provideCompleteDepositPaymentSessionData
     */
    public function testCompleteDepositPaymentSessionWithSignatureVerificationException(
        string $reservationId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
        string $checkoutSessionId,
        array $webhookPayload,
    ): void {
        $this->givenDepositPaymentSessionExists(
            $reservationId,
            $unitIds,
            $unitNames,
            $checkoutSessionId,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $totalUnitDeposit,
            $units,
        );

        $response = $this->postJson(
            self::URL,
            $webhookPayload,
            [
                StripeWebhookConfig::STRIPE_SIGNATURE => 'invalid signature',
            ],
        );

        $response->assertStatus(400);

        $this->thenDepositPaymentSessionShouldExist(
            $reservationId,
            $checkoutSessionId,
            0,
            'open',
            'unpaid',
        );
    }

    /**
     * @param int[]                $unitIds
     * @param string[]             $unitNames
     * @param array<int, mixed>    $units
     * @param array<string, mixed> $webhookPayload
     *
     * @throws DepositPaymentSessionNotFoundException
     * @dataProvider provideCompleteDepositPaymentSessionData
     */
    public function testCompleteDepositPaymentSessionWithDepositPaymentSessionNotFoundException(
        string $reservationId,
        array $unitIds,
        string $agentId,
        string $depositTransferDeadline,
        string $language,
        int $projectId,
        string $prospectId,
        array $unitNames,
        array $units,
        float $totalUnitDeposit,
        string $checkoutSessionId,
        array $webhookPayload,
    ): void {
        $this->givenStripeSignatureCheckServiceValidatesSignature();
        $response = $this->postJson(
            self::URL,
            $webhookPayload,
            [
                StripeWebhookConfig::STRIPE_SIGNATURE => self::SIGNATURE,
            ],
        );

        $response->assertStatus(404);

        $this->thenDepositPaymentSessionShouldNotExist(
            $reservationId,
            $checkoutSessionId,
            0,
            'open',
            'unpaid',
        );
    }

    public function provideCompleteDepositPaymentSessionData(): Generator
    {
        $partialPurposeId1 = $this->faker()->numberBetween(1000, 9999);
        $partialPurposeId2 = $this->faker()->numberBetween(1000, 9999);
        $partialPurposeId3 = $this->faker()->numberBetween(1000, 9999);
        $reservationId = $partialPurposeId1.'-'.$partialPurposeId2.'-'.$partialPurposeId3;

        $agentId = 'da7c58f5-4c74-4722-8b94-7fcf8d857055';
        $depositTransferDeadline = '2020-07-16T16:00:00+00:00';
        $language = 'de';
        $projectId = 80262;
        $prospectId = 'ca50819f-e5a4-40d3-a425-daba3e095407';

        $unitId = $this->faker()->numberBetween(1000, 9999);
        $unitName = $this->faker()->text(10);
        $unitDeposit = $this->faker()->randomFloat(2, 1000, 9999);
        $totalUnitDeposit = $unitDeposit;

        $checkoutSessionId = 'cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2';

        $unitIds = [
            0 => $unitId,
        ];
        $unitNames = [
            0 => $unitName,
        ];
        $units = [
            0 => [
                'id' => $unitId,
                'deposit' => $unitDeposit,
            ],
        ];

        $webhookPayload = $this->getCheckoutSessionCompletedWebhookEventPayload(
            $reservationId,
            $agentId,
            $language,
            $projectId,
            $prospectId,
            $checkoutSessionId,
        );

        yield 'Deposit Payment Session Data with single unit' => [
            $reservationId,
            $unitIds,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $unitNames,
            $units,
            $totalUnitDeposit,
            $checkoutSessionId,
            $webhookPayload,
        ];

        $otherUnitId = $this->faker()->numberBetween(1000, 9999);
        $otherUnitName = $this->faker()->text(10);
        $otherUnitDeposit = $this->faker()->randomFloat(2, 1000, 9999);
        $totalUnitDeposit = $totalUnitDeposit + $otherUnitDeposit;

        $unitIds = array_merge($unitIds, [0 => $otherUnitId]);
        $unitNames = array_merge($unitNames, [0 => $otherUnitName]);
        $units = array_merge(
            $units,
            [
                0 => [
                    'id' => $otherUnitId,
                    'deposit' => $otherUnitDeposit,
                ],
            ]
        );

        yield 'Deposit Payment Session Data with multiple units' => [
            $reservationId,
            $unitIds,
            $agentId,
            $depositTransferDeadline,
            $language,
            $projectId,
            $prospectId,
            $unitNames,
            $units,
            $totalUnitDeposit,
            $checkoutSessionId,
            $webhookPayload,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getCheckoutSessionCompletedWebhookEventPayload(
        string $reservationId,
        string $agentId,
        string $language,
        int $projectId,
        string $prospectId,
        string $checkoutSessionId,
    ): array {
        return [
            'id' => 'evt_1LDxd7LHVthCw2smu1a2hiD7',
            'object' => 'event',
            'api_version' => '2020-08-27',
            'created' => 1656019920,
            'data' => [
                'object' => [
                    'id' => $checkoutSessionId,
                    'object' => 'checkout.session',
                    'after_expiration' => null,
                    'allow_promotion_codes' => null,
                    'amount_subtotal' => 300000,
                    'amount_total' => 300000,
                    'automatic_tax' => [
                        'enabled' => false,
                        'status' => null,
                    ],
                    'billing_address_collection' => null,
                    'cancel_url' => 'https://google.com/?session_id={CHECKOUT_SESSION_ID}',
                    'client_reference_id' => null,
                    'consent' => null,
                    'consent_collection' => null,
                    'currency' => 'eur',
                    'customer' => 'cus_LvpHBaHVtRJVqY',
                    'customer_creation' => 'always',
                    'customer_details' => [
                        'address' => [
                            'city' => null,
                            'country' => 'DE',
                            'line1' => null,
                            'line2' => null,
                            'postal_code' => null,
                            'state' => null,
                        ],
                        'email' => 'nam.anh.nguyen@allmyhomes.com',
                        'name' => 'Nam Anh',
                        'phone' => null,
                        'tax_exempt' => 'none',
                        'tax_ids' => [
                        ],
                    ],
                    'customer_email' => null,
                    'expires_at' => 1656064800,
                    'livemode' => false,
                    'locale' => $language,
                    'metadata' => [
                        'prospect_id' => $prospectId,
                        'agent_id' => $agentId,
                        'reservation_id' => $reservationId,
                        'project_id' => (string) $projectId,
                    ],
                    'mode' => 'payment',
                    'payment_intent' => 'pi_3LDxcLLHVthCw2sm1agyarEt',
                    'payment_link' => null,
                    'payment_method_options' => [
                    ],
                    'payment_method_types' => [
                        0 => 'card',
                    ],
                    'payment_status' => 'paid',
                    'phone_number_collection' => [
                        'enabled' => false,
                    ],
                    'recovered_from' => null,
                    'setup_intent' => null,
                    'shipping' => null,
                    'shipping_address_collection' => null,
                    'shipping_options' => [
                    ],
                    'shipping_rate' => null,
                    'status' => 'complete',
                    'submit_type' => null,
                    'subscription' => null,
                    'success_url' => 'https://google.com/?session_id={CHECKOUT_SESSION_ID}',
                    'total_details' => [
                        'amount_discount' => 0,
                        'amount_shipping' => 0,
                        'amount_tax' => 0,
                    ],
                    'url' => null,
                ],
            ],
            'livemode' => false,
            'pending_webhooks' => 1,
            'request' => [
                'id' => null,
                'idempotency_key' => null,
            ],
            'type' => 'checkout.session.completed',
        ];
    }
}
