<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentEmail\Aggregate;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\DepositPaymentEmail\Event\DepositPaymentEmailSendingFailed;
use Allmyhomes\Domain\DepositPaymentEmail\Event\DepositPaymentEmailSentToProspect;
use Allmyhomes\Domain\DepositPaymentEmail\Mail\MailerInterface;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\DepositPaymentEmailData;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectEmail;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectFirstName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectLastName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectSalutation;
use Allmyhomes\Domain\ValueObject\CreatedAt;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\AbstractAggregateRoot;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Aggregate\AggregateId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\ImmutableState;

final class DepositPaymentEmail extends AbstractAggregateRoot
{
    /** @var DepositPaymentEmailState&ImmutableState */
    protected DepositPaymentEmailState|ImmutableState $state;

    public const TYPE = Context::NAME.'DepositPaymentEmail';

    public static function sendNewDepositPaymentEmail(
        ProspectId $prospectId,
        ReservationId $reservationId,
        CheckoutSessionId $checkoutSessionId,
        ProspectEmail $prospectEmail,
        ?ProspectFirstName $prospectFirstName,
        ProspectLastName $prospectLastName,
        ?ProspectSalutation $prospectSalutation,
        UnitCollection $unitCollection,
        CheckoutSessionUrl $checkoutSessionUrl,
        ExpiresAt $expiresAt,
        Language $language,
        MailerInterface $mailer,
    ): self {
        $self = new self();

        $self->sendDepositPaymentEmail(
            $prospectId,
            $reservationId,
            $checkoutSessionId,
            $prospectEmail,
            $prospectFirstName,
            $prospectLastName,
            $prospectSalutation,
            $unitCollection,
            $checkoutSessionUrl,
            $expiresAt,
            $language,
            $mailer,
            ErrorCount::fromInt(0),
        );

        return $self;
    }

    private function sendDepositPaymentEmail(
        ProspectId $prospectId,
        ReservationId $reservationId,
        CheckoutSessionId $checkoutSessionId,
        ProspectEmail $prospectEmail,
        ?ProspectFirstName $prospectFirstName,
        ProspectLastName $prospectLastName,
        ?ProspectSalutation $prospectSalutation,
        UnitCollection $unitCollection,
        CheckoutSessionUrl $checkoutSessionUrl,
        ExpiresAt $expiresAt,
        Language $language,
        MailerInterface $mailer,
        ErrorCount $errorCount,
    ): void {
        $requestId = $mailer->sendEmail(
            DepositPaymentEmailData::fromArray([
                DepositPaymentEmailData::RESERVATION_ID => $reservationId->toString(),
                DepositPaymentEmailData::LANGUAGE => $language->toString(),
                DepositPaymentEmailData::PROSPECT_ID => $prospectId->toString(),
                DepositPaymentEmailData::PROSPECT_EMAIL => $prospectEmail->toString(),
                DepositPaymentEmailData::PROSPECT_FIRST_NAME => $prospectFirstName?->toString(),
                DepositPaymentEmailData::PROSPECT_LAST_NAME => $prospectLastName->toString(),
                DepositPaymentEmailData::PROSPECT_SALUTATION => $prospectSalutation?->toInt(),
                DepositPaymentEmailData::UNIT_COLLECTION => $unitCollection->toArray(),
                DepositPaymentEmailData::CHECKOUT_SESSION_URL => $checkoutSessionUrl->toString(),
                DepositPaymentEmailData::EXPIRES_AT => $expiresAt->toString(),
            ])
        );

        if (empty($requestId)) {
            $this->recordThat(DepositPaymentEmailSendingFailed::fromRecordData([
                DepositPaymentEmailSendingFailed::PROSPECT_ID => $prospectId,
                DepositPaymentEmailSendingFailed::RESERVATION_ID => $reservationId,
                DepositPaymentEmailSendingFailed::CHECKOUT_SESSION_ID => $checkoutSessionId,
                DepositPaymentEmailSendingFailed::CHECKOUT_SESSION_URL => $checkoutSessionUrl,
                DepositPaymentEmailSendingFailed::EXPIRES_AT => $expiresAt,
                DepositPaymentEmailSendingFailed::ERROR_COUNT => $errorCount->increase(),
                DepositPaymentEmailSendingFailed::CREATED_AT => CreatedAt::fromDateTime(new \DateTimeImmutable()),
            ]));
        } else {
            $this->recordThat(DepositPaymentEmailSentToProspect::fromRecordData([
                DepositPaymentEmailSentToProspect::PROSPECT_ID => $prospectId,
                DepositPaymentEmailSentToProspect::RESERVATION_ID => $reservationId,
                DepositPaymentEmailSentToProspect::CHECKOUT_SESSION_ID => $checkoutSessionId,
                DepositPaymentEmailSentToProspect::REQUEST_ID => $requestId,
                DepositPaymentEmailSentToProspect::CHECKOUT_SESSION_URL => $checkoutSessionUrl,
                DepositPaymentEmailSentToProspect::EXPIRES_AT => $expiresAt,
                DepositPaymentEmailSentToProspect::CREATED_AT => CreatedAt::fromDateTime(new \DateTimeImmutable()),
            ]));
        }
    }

    public function whenDepositPaymentEmailSentToProspect(DepositPaymentEmailSentToProspect $event): DepositPaymentEmailState
    {
        return DepositPaymentEmailState::fromRecordData([
            DepositPaymentEmailState::PROSPECT_ID => $event->prospectId(),
            DepositPaymentEmailState::RESERVATION_ID => $event->reservationId(),
            DepositPaymentEmailState::CHECKOUT_SESSION_ID => $event->checkoutSessionId(),
            DepositPaymentEmailState::REQUEST_ID => $event->requestId(),
            DepositPaymentEmailState::ERROR_COUNT => ErrorCount::fromInt(0),
        ]);
    }

    public function whenDepositPaymentEmailSendingFailed(
        DepositPaymentEmailSendingFailed $event
    ): DepositPaymentEmailState {
        return DepositPaymentEmailState::fromRecordData([
            DepositPaymentEmailState::PROSPECT_ID => $event->prospectId(),
            DepositPaymentEmailState::RESERVATION_ID => $event->reservationId(),
            DepositPaymentEmailState::CHECKOUT_SESSION_ID => $event->checkoutSessionId(),
            DepositPaymentEmailState::ERROR_COUNT => $event->errorCount(),
        ]);
    }

    public function aggregateId(): AggregateId
    {
        return AggregateId::fromValueObject($this->state->reservationId());
    }
}
