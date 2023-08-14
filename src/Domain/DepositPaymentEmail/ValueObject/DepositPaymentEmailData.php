<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentEmail\ValueObject;

use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\UnitCollection;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectEmail;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectFirstName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectLastName;
use Allmyhomes\Domain\Prospect\ValueObject\ProspectSalutation;
use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Domain\ValueObject\ProspectId;
use Allmyhomes\Domain\ValueObject\ReservationId;

final class DepositPaymentEmailData
{
    public const RESERVATION_ID = 'reservation_id';
    public const LANGUAGE = 'language';
    public const PROSPECT_ID = 'prospect_id';
    public const PROSPECT_EMAIL = 'prospect_email';
    public const PROSPECT_FIRST_NAME = 'prospect_first_name';
    public const PROSPECT_LAST_NAME = 'prospect_last_name';
    public const PROSPECT_SALUTATION = 'prospect_salutation';
    public const UNIT_COLLECTION = 'unit_collection';
    public const CHECKOUT_SESSION_URL = 'checkout_session_url';
    public const EXPIRES_AT = 'expires_at';

    /**
     * @param array<string, mixed> $emailData
     */
    public static function fromArray(array $emailData): self
    {
        return new self(
            ReservationId::fromString($emailData[self::RESERVATION_ID]),
            Language::fromString($emailData[self::LANGUAGE]),
            ProspectId::fromString($emailData[self::PROSPECT_ID]),
            ProspectEmail::fromString($emailData[self::PROSPECT_EMAIL]),
            $emailData[self::PROSPECT_FIRST_NAME] ? ProspectFirstName::fromString($emailData[self::PROSPECT_FIRST_NAME]) : null,
            ProspectLastName::fromString($emailData[self::PROSPECT_LAST_NAME]),
            $emailData[self::PROSPECT_SALUTATION] ? ProspectSalutation::fromInt($emailData[self::PROSPECT_SALUTATION]) : null,
            UnitCollection::fromArray($emailData[self::UNIT_COLLECTION]),
            CheckoutSessionUrl::fromString($emailData[self::CHECKOUT_SESSION_URL]),
            ExpiresAt::fromString($emailData[self::EXPIRES_AT]),
        );
    }

    private function __construct(
        private ReservationId $reservationId,
        private Language $language,
        private ProspectId $prospectId,
        private ProspectEmail $prospectEmail,
        private ?ProspectFirstName $prospectFirstName,
        private ProspectLastName $prospectLastName,
        private ?ProspectSalutation $prospectSalutation,
        private UnitCollection $unitCollection,
        private CheckoutSessionUrl $checkoutSessionUrl,
        private ExpiresAt $expiresAt,
    ) {
    }

    public function reservationId(): ReservationId
    {
        return $this->reservationId;
    }

    public function language(): Language
    {
        return $this->language;
    }

    public function prospectId(): ProspectId
    {
        return $this->prospectId;
    }

    public function prospectEmail(): ProspectEmail
    {
        return $this->prospectEmail;
    }

    public function prospectFirstName(): ?ProspectFirstName
    {
        return $this->prospectFirstName;
    }

    public function prospectLastName(): ProspectLastName
    {
        return $this->prospectLastName;
    }

    public function prospectSalutation(): ?ProspectSalutation
    {
        return $this->prospectSalutation;
    }

    public function unitCollection(): UnitCollection
    {
        return $this->unitCollection;
    }

    public function checkoutSessionUrl(): CheckoutSessionUrl
    {
        return $this->checkoutSessionUrl;
    }

    public function expiresAt(): ExpiresAt
    {
        return $this->expiresAt;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::RESERVATION_ID => $this->reservationId->toString(),
            self::LANGUAGE => $this->language->toString(),
            self::PROSPECT_ID => $this->prospectId->toString(),
            self::PROSPECT_EMAIL => $this->prospectEmail->toString(),
            self::PROSPECT_FIRST_NAME => $this->prospectFirstName?->toString(),
            self::PROSPECT_LAST_NAME => $this->prospectLastName->toString(),
            self::PROSPECT_SALUTATION => $this->prospectSalutation?->toInt(),
            self::UNIT_COLLECTION => $this->unitCollection->toArray(),
            self::CHECKOUT_SESSION_URL => $this->checkoutSessionUrl->toString(),
            self::EXPIRES_AT => $this->expiresAt->toString(),
        ];
    }
}
