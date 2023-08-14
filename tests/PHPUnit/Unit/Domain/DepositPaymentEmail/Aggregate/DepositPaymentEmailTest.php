<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentEmail\Aggregate;

use Allmyhomes\Domain\DepositPaymentEmail\Aggregate\DepositPaymentEmail;
use Allmyhomes\Domain\DepositPaymentEmail\Aggregate\DepositPaymentEmailState;
use Allmyhomes\Domain\DepositPaymentEmail\Mail\MailerInterface;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\DepositPaymentEmailData;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\RequestId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectEmail;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectFirstName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectLastName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectSalutation;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DepositPaymentEmailTest extends TestCase
{
    private ProspectId $prospectId;
    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private RequestId $requestId;
    private ProspectEmail $prospectEmail;
    private ProspectFirstName $prospectFirstName;
    private ProspectLastName $prospectLastName;
    private ProspectSalutation $prospectSalutation;
    private UnitCollection $unitCollection;
    private CheckoutSessionUrl $checkoutSessionUrl;
    private ExpiresAt $expiresAt;
    private Language $language;

    private DepositPaymentEmailData $depositPaymentEmailData;

    /**
     * @var MailerInterface&MockObject
     */
    private MailerInterface $mailer;

    public function setUp(): void
    {
        parent::setUp();

        $this->prospectId = ProspectId::fromString('ca50819f-e5a4-40d3-a425-daba3e095407');
        $this->reservationId = ReservationId::fromString('1234-1234-1234');
        $this->checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2');
        $this->requestId = RequestId::fromString('request id');
        $this->prospectEmail = ProspectEmail::fromString('max.mustermann@gmail.com');
        $this->prospectFirstName = ProspectFirstName::fromString('Max');
        $this->prospectLastName = ProspectLastName::fromString('Mustermann');
        $this->prospectSalutation = ProspectSalutation::fromInt(0);
        $this->unitCollection = UnitCollection::fromArray([
            0 => [
                'id' => 1,
                'name' => 'WE 1',
                'deposit' => 3000.00,
            ],
        ]);
        $this->checkoutSessionUrl = CheckoutSessionUrl::fromString('https://www.example.com');
        $this->expiresAt = ExpiresAt::fromSeconds(1653004800);
        $this->language = Language::fromString('de');

        $this->depositPaymentEmailData = DepositPaymentEmailData::fromArray([
            DepositPaymentEmailData::RESERVATION_ID => $this->reservationId->toString(),
            DepositPaymentEmailData::LANGUAGE => $this->language->toString(),
            DepositPaymentEmailData::PROSPECT_ID => $this->prospectId->toString(),
            DepositPaymentEmailData::PROSPECT_EMAIL => $this->prospectEmail->toString(),
            DepositPaymentEmailData::PROSPECT_FIRST_NAME => $this->prospectFirstName->toString(),
            DepositPaymentEmailData::PROSPECT_LAST_NAME => $this->prospectLastName->toString(),
            DepositPaymentEmailData::PROSPECT_SALUTATION => $this->prospectSalutation->toInt(),
            DepositPaymentEmailData::UNIT_COLLECTION => $this->unitCollection->toArray(),
            DepositPaymentEmailData::CHECKOUT_SESSION_URL => $this->checkoutSessionUrl->toString(),
            DepositPaymentEmailData::EXPIRES_AT => $this->expiresAt->toString(),
        ]);

        $this->mailer = $this->createMock(MailerInterface::class);
    }

    public function testSendNewDepositPaymentEmail(): void
    {
        $this->mailer
            ->expects(self::once())
            ->method('sendEmail')
            ->with($this->depositPaymentEmailData)
            ->willReturn($this->requestId);

        $depositPaymentEmail = DepositPaymentEmail::sendNewDepositPaymentEmail(
            $this->prospectId,
            $this->reservationId,
            $this->checkoutSessionId,
            $this->prospectEmail,
            $this->prospectFirstName,
            $this->prospectLastName,
            $this->prospectSalutation,
            $this->unitCollection,
            $this->checkoutSessionUrl,
            $this->expiresAt,
            $this->language,
            $this->mailer,
        );

        self::assertEquals(
            [
                DepositPaymentEmailState::PROSPECT_ID => $this->prospectId->toString(),
                DepositPaymentEmailState::RESERVATION_ID => $this->reservationId->toString(),
                DepositPaymentEmailState::CHECKOUT_SESSION_ID => $this->checkoutSessionId->toString(),
                DepositPaymentEmailState::REQUEST_ID => $this->requestId->toString(),
                DepositPaymentEmailState::ERROR_COUNT => ErrorCount::fromInt(0)->toInt(),
            ],
            $depositPaymentEmail->state()->toArray(),
            'created deposit payment email aggregate array does not match deposit payment email array.'
        );
    }

    public function testSendNewDepositPaymentEmailWithEmptySendEmailResult(): void
    {
        $this->mailer
            ->expects(self::once())
            ->method('sendEmail')
            ->with($this->depositPaymentEmailData)
            ->willReturn(null);

        $depositPaymentEmail = DepositPaymentEmail::sendNewDepositPaymentEmail(
            $this->prospectId,
            $this->reservationId,
            $this->checkoutSessionId,
            $this->prospectEmail,
            $this->prospectFirstName,
            $this->prospectLastName,
            $this->prospectSalutation,
            $this->unitCollection,
            $this->checkoutSessionUrl,
            $this->expiresAt,
            $this->language,
            $this->mailer,
        );

        self::assertEquals(
            [
                DepositPaymentEmailState::PROSPECT_ID => $this->prospectId->toString(),
                DepositPaymentEmailState::RESERVATION_ID => $this->reservationId->toString(),
                DepositPaymentEmailState::CHECKOUT_SESSION_ID => $this->checkoutSessionId->toString(),
                DepositPaymentEmailState::REQUEST_ID => null,
                DepositPaymentEmailState::ERROR_COUNT => ErrorCount::fromInt(1)->toInt(),
            ],
            $depositPaymentEmail->state()->toArray(),
            'created deposit payment email aggregate array does not match deposit payment email array.'
        );
    }

    public function testAggregateId(): void
    {
        $this->mailer
            ->expects(self::once())
            ->method('sendEmail')
            ->with($this->depositPaymentEmailData)
            ->willReturn($this->requestId);

        $depositPaymentEmail = DepositPaymentEmail::sendNewDepositPaymentEmail(
            $this->prospectId,
            $this->reservationId,
            $this->checkoutSessionId,
            $this->prospectEmail,
            $this->prospectFirstName,
            $this->prospectLastName,
            $this->prospectSalutation,
            $this->unitCollection,
            $this->checkoutSessionUrl,
            $this->expiresAt,
            $this->language,
            $this->mailer,
        );

        self::assertInstanceOf(
            AggregateId::class,
            $depositPaymentEmail->aggregateId(),
            'aggregate id of deposit payment email does not match expected class: AggregateId.'
        );
        self::assertEquals(
            $this->reservationId->toString(),
            $depositPaymentEmail->aggregateId()->toString(),
            'aggregate id of deposit payment email does not match expected string.'
        );
    }
}
