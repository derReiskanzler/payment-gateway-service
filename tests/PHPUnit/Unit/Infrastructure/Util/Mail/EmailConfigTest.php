<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Util\Mail;

use Allmyhomes\Domain\ValueObject\Language;
use Allmyhomes\Infrastructure\Util\Mail\EmailConfig;
use Generator;
use PHPUnit\Framework\TestCase;

final class EmailConfigTest extends TestCase
{
    /**
     * @param array<string, mixed> $config
     * @dataProvider provideEmailConfigData
     */
    public function testFromArray(array $config): void
    {
        $emailConfig = EmailConfig::fromArray($config);

        self::assertInstanceOf(
            EmailConfig::class,
            $emailConfig,
            'email config does not match expected class: EmailConfig.'
        );
    }

    /**
     * @param array<string, mixed> $config
     * @dataProvider provideEmailConfigData
     */
    public function testSenderEmail(array $config): void
    {
        $emailConfig = EmailConfig::fromArray($config);

        self::assertEquals(
            $config[EmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_EMAIL],
            $emailConfig->senderEmail(),
            'email config sender email does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $config
     * @dataProvider provideEmailConfigData
     */
    public function testSenderName(array $config): void
    {
        $emailConfig = EmailConfig::fromArray($config);

        self::assertEquals(
            $config[EmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_NAME],
            $emailConfig->senderName(),
            'email config sender name does not match expected string.'
        );
    }

    /**
     * @param array<string, mixed> $config
     * @dataProvider provideEmailConfigData
     */
    public function testSubjects(array $config): void
    {
        $emailConfig = EmailConfig::fromArray($config);

        self::assertEquals(
            $config[EmailConfig::DEPOSIT_PAYMENT_EMAIL_SUBJECTS],
            $emailConfig->subjects(),
            'email config subjects does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $config
     * @dataProvider provideEmailConfigData
     */
    public function testGetSubjectByLanguage(array $config, string $language): void
    {
        $emailConfig = EmailConfig::fromArray($config);

        self::assertEquals(
            $config[EmailConfig::DEPOSIT_PAYMENT_EMAIL_SUBJECTS][$language],
            $emailConfig->getSubjectByLanguage(Language::fromString($language)),
            'email config subjects does not match expected array.'
        );
    }

    /**
     * @param array<string, mixed> $config
     * @dataProvider provideEmailConfigData
     */
    public function testToArray(array $config): void
    {
        $emailConfig = EmailConfig::fromArray($config);

        self::assertEquals(
            $config,
            $emailConfig->toArray(),
            'email config to array does not match expected array.'
        );
    }

    public function provideEmailConfigData(): Generator
    {
        yield 'EmailConfig with german data' => [
            'EmailConfig array data' => [
                EmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_EMAIL => 'reservations@allmyhomes.com',
                EmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_NAME => 'AllMyHomes',
                EmailConfig::DEPOSIT_PAYMENT_EMAIL_SUBJECTS => [
                    'de' => 'Zahlungsinformationen der Reservierungsgebühren',
                    'en' => 'Deposit payment details',
                ],
            ],
            'language' => 'de',
        ];

        yield 'EmailConfig with english data' => [
            'EmailConfig array data' => [
                EmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_EMAIL => 'reservations@allmyhomes.com',
                EmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_NAME => 'AllMyHomes',
                EmailConfig::DEPOSIT_PAYMENT_EMAIL_SUBJECTS => [
                    'de' => 'Zahlungsinformationen der Reservierungsgebühren',
                    'en' => 'Deposit payment details',
                ],
            ],
            'language' => 'en',
        ];
    }
}
