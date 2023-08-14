<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\Mail;

use Allmyhomes\Domain\ValueObject\Language;

final class EmailConfig
{
    public const DEPOSIT_PAYMENT_EMAIL_SENDER_EMAIL = 'deposit_payment_email_sender_email';
    public const DEPOSIT_PAYMENT_EMAIL_SENDER_NAME = 'deposit_payment_email_sender_name';
    public const DEPOSIT_PAYMENT_EMAIL_SUBJECTS = 'deposit_payment_email_subjects';

    /**
     * @param array<string, mixed> $configData
     */
    public static function fromArray(array $configData): self
    {
        return new self(
            $configData[self::DEPOSIT_PAYMENT_EMAIL_SENDER_EMAIL],
            $configData[self::DEPOSIT_PAYMENT_EMAIL_SENDER_NAME],
            $configData[self::DEPOSIT_PAYMENT_EMAIL_SUBJECTS],
        );
    }

    /**
     * @param string[] $subjects
     */
    private function __construct(
        private string $senderEmail,
        private string $senderName,
        private array $subjects,
    ) {
    }

    public function senderEmail(): string
    {
        return $this->senderEmail;
    }

    public function senderName(): string
    {
        return $this->senderName;
    }

    /**
     * @return string[]
     */
    public function subjects(): array
    {
        return $this->subjects;
    }

    public function getSubjectByLanguage(Language $language): string
    {
        return $this->subjects[$language->toString()];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::DEPOSIT_PAYMENT_EMAIL_SENDER_EMAIL => $this->senderEmail,
            self::DEPOSIT_PAYMENT_EMAIL_SENDER_NAME => $this->senderName,
            self::DEPOSIT_PAYMENT_EMAIL_SUBJECTS => $this->subjects,
        ];
    }
}
