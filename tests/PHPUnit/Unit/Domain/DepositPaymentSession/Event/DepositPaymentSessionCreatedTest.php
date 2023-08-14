<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentSession\Event;

use Allmyhomes\Domain\DepositPaymentSession\Event\DepositPaymentSessionCreated;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\Currency;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CustomerId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentIntentId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\ValueObject\AgentId;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProjectId;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Domain\ValueObject\TotalUnitDeposit;
use Allmyhomes\Domain\ValueObject\UnitIdCollection;
use PHPUnit\Framework\TestCase;

final class DepositPaymentSessionCreatedTest extends TestCase
{
    private DepositPaymentSessionCreated $depositPaymentSessionCreated;

    private ReservationId $id;
    private AgentId $agentId;
    private Language $language;
    private ProjectId $projectId;
    private ProspectId $prospectId;
    private TotalUnitDeposit $totalUnitDeposit;
    private UnitIdCollection $unitIds;

    private CheckoutSessionId $checkoutSessionId;
    private CheckoutSessionStatus $checkoutSessionStatus;
    private CheckoutSessionUrl $checkoutSessionUrl;
    private CreatedAt $createdAt;
    private Currency $currency;
    private ?CustomerId $customerId;
    private ExpiresAt $expiresAt;
    private PaymentIntentId $paymentIntentId;
    private PaymentStatus $paymentStatus;

    public function setUp(): void
    {
        parent::setUp();

        $unitCollection = UnitCollection::fromArray([
            0 => [
                'id' => 1,
                'name' => 'WE 1',
                'deposit' => 3000.00,
            ],
        ]);

        $this->id = ReservationId::fromString('1234-1234-1234');
        $this->agentId = AgentId::fromString('b72a7cdc-0d36-411e-b897-c9dd0a79c318');
        $this->language = Language::fromString('de');
        $this->projectId = ProjectId::fromInt(80262);
        $this->prospectId = ProspectId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0');
        $this->totalUnitDeposit = $unitCollection->totalUnitDeposit();
        $this->unitIds = $unitCollection->idCollection();

        $this->checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1TpDn0YJ3sKoj7lbOq1DbWGrnGQMPT4Kdg837nN8pyFarROhHuvOp71BC');
        $this->checkoutSessionStatus = CheckoutSessionStatus::fromString('open');
        $this->checkoutSessionUrl = CheckoutSessionUrl::fromString('https://checkout.stripe.com/pay/cs_test_a10MHweko6m628yFFNm7lQQscNp9f9qs2JL7Hzdfz3JdReLBg82UNMmrLo#fidkdWxOYHwnPyd1blpxYHZxWjA0TjE0PW1PTVdTPXZ1YzVUbTJra21hZGNvQTVfXDE1SHxUVDNdcmRLNmg0UFNyMDZnMWYyam5UdzxQYEtJNH1wNmJndU58NWlRMT1GQk5gYmRyalJHNXdKNTVCX1xSfFZOcScpJ2N3amhWYHdzYHcnP3F3cGApJ2lkfGpwcVF8dWAnPyd2bGtiaWBabHFgaCcpJ2BrZGdpYFVpZGZgbWppYWB3dic%2FcXdwYCkndXdgaWpkYUNqa3EnPydXamdqcWoneCUl');
        $this->createdAt = CreatedAt::fromDateTime(new \DateTimeImmutable());
        $this->currency = Currency::fromString('eur');
        $this->customerId = CustomerId::fromString('customer id');
        $this->expiresAt = ExpiresAt::fromSeconds(1652873083);
        $this->paymentIntentId = PaymentIntentId::fromString('pi_3KSOXmJHRV8spf0Q1Vaclh9l');
        $this->paymentStatus = PaymentStatus::fromString('unpaid');

        $this->depositPaymentSessionCreated = DepositPaymentSessionCreated::fromRecordData([
            DepositPaymentSessionCreated::RESERVATION_ID => $this->id,
            DepositPaymentSessionCreated::AGENT_ID => $this->agentId,
            DepositPaymentSessionCreated::LANGUAGE => $this->language,
            DepositPaymentSessionCreated::PROJECT_ID => $this->projectId,
            DepositPaymentSessionCreated::PROSPECT_ID => $this->prospectId,
            DepositPaymentSessionCreated::TOTAL_UNIT_DEPOSIT => $this->totalUnitDeposit,
            DepositPaymentSessionCreated::UNIT_IDS => $this->unitIds,

            DepositPaymentSessionCreated::CHECKOUT_SESSION_ID => $this->checkoutSessionId,
            DepositPaymentSessionCreated::CHECKOUT_SESSION_STATUS => $this->checkoutSessionStatus,
            DepositPaymentSessionCreated::CHECKOUT_SESSION_URL => $this->checkoutSessionUrl,
            DepositPaymentSessionCreated::CREATED_AT => $this->createdAt,
            DepositPaymentSessionCreated::CURRENCY => $this->currency,
            DepositPaymentSessionCreated::CUSTOMER_ID => $this->customerId,
            DepositPaymentSessionCreated::EXPIRES_AT => $this->expiresAt,
            DepositPaymentSessionCreated::PAYMENT_INTENT_ID => $this->paymentIntentId,
            DepositPaymentSessionCreated::PAYMENT_STATUS => $this->paymentStatus,
        ]);
    }

    public function testEventName(): void
    {
        self::assertEquals(
            'PaymentGateway.DepositPaymentSessionCreated',
            $this->depositPaymentSessionCreated->eventName(),
            'event name from created deposit payment session created event does not match expected event name.',
        );
    }

    public function testId(): void
    {
        self::assertEquals(
            $this->id->toString(),
            $this->depositPaymentSessionCreated->reservationId()->toString(),
            'id from created deposit payment session created event does not match expected id.',
        );
    }

    public function testAgentId(): void
    {
        self::assertEquals(
            $this->agentId->toString(),
            $this->depositPaymentSessionCreated->agentId()->toString(),
            'agentId from created deposit payment session created event does not match expected agentId.',
        );
    }

    public function testLanguage(): void
    {
        self::assertEquals(
            $this->language->toString(),
            $this->depositPaymentSessionCreated->language()->toString(),
            'language from created deposit payment session created event does not match expected language.',
        );
    }

    public function testProjectId(): void
    {
        self::assertEquals(
            $this->projectId->toInt(),
            $this->depositPaymentSessionCreated->projectId()->toInt(),
            'projectId from created deposit payment session created event does not match expected projectId.',
        );
    }

    public function testProspectId(): void
    {
        self::assertEquals(
            $this->prospectId->toString(),
            $this->depositPaymentSessionCreated->prospectId()->toString(),
            'prospectId from created deposit payment session created event does not match expected prospectId.',
        );
    }

    public function testTotalUnitDeposit(): void
    {
        self::assertEquals(
            $this->totalUnitDeposit->toFloat(),
            $this->depositPaymentSessionCreated->totalUnitDeposit()->toFloat(),
            'totalUnitDeposit from created deposit payment session created event does not match expected totalUnitDeposit.',
        );
    }

    public function testUnitIds(): void
    {
        self::assertEquals(
            $this->unitIds->toArray(),
            $this->depositPaymentSessionCreated->unitIds()->toArray(),
            'unitIds from created deposit payment session created event does not match expected unitIds.',
        );
    }

    public function testCheckoutSessionId(): void
    {
        self::assertEquals(
            $this->checkoutSessionId->toString(),
            $this->depositPaymentSessionCreated->checkoutSessionId()->toString(),
            'checkoutSessionId from created deposit payment session created event does not match expected checkoutSessionId.',
        );
    }

    public function testCheckoutSessionStatus(): void
    {
        self::assertEquals(
            $this->checkoutSessionStatus->toString(),
            $this->depositPaymentSessionCreated->checkoutSessionStatus()?->toString(),
            'checkoutSessionStatus from created deposit payment session created event does not match expected checkoutSessionStatus.',
        );
    }

    public function testCheckoutSessionUrl(): void
    {
        self::assertEquals(
            $this->checkoutSessionUrl->toString(),
            $this->depositPaymentSessionCreated->checkoutSessionUrl()?->toString(),
            'checkoutSessionUrl from created deposit payment session created event does not match expected checkoutSessionUrl.',
        );
    }

    public function testCreatedAt(): void
    {
        self::assertEquals(
            $this->createdAt->toString(),
            $this->depositPaymentSessionCreated->createdAt()->toString(),
            'createdAt from created deposit payment session created event does not match expected createdAt.',
        );
    }

    public function testCurrency(): void
    {
        self::assertEquals(
            $this->currency->toString(),
            $this->depositPaymentSessionCreated->currency()?->toString(),
            'currency from created deposit payment session created event does not match expected currency.',
        );
    }

    public function testCustomerId(): void
    {
        self::assertEquals(
            $this->customerId?->toString(),
            $this->depositPaymentSessionCreated->customerId()?->toString(),
            'customerId from created deposit payment session created event does not match expected customerId.',
        );
    }

    public function testExpiresAt(): void
    {
        self::assertEquals(
            $this->expiresAt->toSeconds(),
            $this->depositPaymentSessionCreated->expiresAt()->toSeconds(),
            'expiresAt from created deposit payment session created event does not match expected expiresAt.',
        );
    }

    public function testPaymentIntentId(): void
    {
        self::assertEquals(
            $this->paymentIntentId->toString(),
            $this->depositPaymentSessionCreated->paymentIntentId()?->toString(),
            'paymentIntentId from created deposit payment session created event does not match expected paymentIntentId.',
        );
    }

    public function testPaymentStatus(): void
    {
        self::assertEquals(
            $this->paymentStatus->toString(),
            $this->depositPaymentSessionCreated->paymentStatus()->toString(),
            'paymentStatus from created deposit payment session created event does not match expected paymentStatus.',
        );
    }
}
