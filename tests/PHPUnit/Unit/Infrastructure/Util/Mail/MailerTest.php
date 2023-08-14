<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Util\Mail;

use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\DepositPaymentEmailData;
use Allmyhomes\Infrastructure\Util\Mail\EmailConfig;
use Allmyhomes\Infrastructure\Util\Mail\Helper\MailRendererHelperInterface;
use Allmyhomes\Infrastructure\Util\Mail\Helper\MailTranslationHelperInterface;
use Allmyhomes\Infrastructure\Util\Mail\Mailer;
use Allmyhomes\MailRendererClient\Exceptions\FailedToSendMailWithMailRendererException;
use Allmyhomes\MailRendererClient\Exceptions\MissingFieldsInMailRendererRequestException;
use Allmyhomes\MailRendererClient\Services\MailRendererClient;
use Generator;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tests\CreatesApplicationTrait;

final class MailerTest extends TestCase
{
    use CreatesApplicationTrait;

    private Mailer $mailer;

    /**
     * @var MailRendererClient&MockObject
     */
    private MailRendererClient $mailRendererClient;
    /**
     * @var MailTranslationHelperInterface&MockObject
     */
    private MailTranslationHelperInterface $mailTranslationHelper;
    /**
     * @var MailRendererHelperInterface&MockObject
     */
    private MailRendererHelperInterface $mailRendererHelper;
    /**
     * @var LoggerInterface&MockObject
     */
    private LoggerInterface $logger;

    private EmailConfig $emailConfig;

    public function setUp(): void
    {
        parent::setUp();

        $this->createApplication();

        $this->mailRendererClient = $this->createMock(MailRendererClient::class);
        $this->mailTranslationHelper = $this->createMock(MailTranslationHelperInterface::class);
        $this->mailRendererHelper = $this->createMock(MailRendererHelperInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->emailConfig = EmailConfig::fromArray([
            EmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_EMAIL => 'reservations@allmyhomes.com',
            EmailConfig::DEPOSIT_PAYMENT_EMAIL_SENDER_NAME => 'AllMyHomes',
            EmailConfig::DEPOSIT_PAYMENT_EMAIL_SUBJECTS => [
                'de' => 'Zahlungsinformationen der Reservierungsgebühren',
                'en' => 'Deposit payment details',
            ],
        ]);

        $this->mailer = new Mailer(
            $this->mailRendererClient,
            $this->mailTranslationHelper,
            $this->mailRendererHelper,
            $this->emailConfig,
            $this->logger,
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailData
     * @dataProvider provideMailerSendEmailData
     */
    public function testSendEmail(
        array $depositPaymentEmailData,
        string $language
    ): void {
        $data = DepositPaymentEmailData::fromArray($depositPaymentEmailData);
        $renderedMjmlTemplate = View::make('mails.'.$language.'.deposit_payment_mjml', ['data' => $data])->render();
        $renderedPlainTemplate = View::make('mails.'.$language.'.deposit_payment_plain', ['data' => $data])->render();

        $this->mailRendererHelper
            ->expects(self::exactly(2))
            ->method('render')
            ->willReturnOnConsecutiveCalls(
                $renderedMjmlTemplate,
                $renderedPlainTemplate,
            );

        $this->mailTranslationHelper
            ->expects(self::once())
            ->method('setLocale')
            ->with($data->language()->toString());

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setSubject')
            ->with($this->emailConfig->getSubjectByLanguage($data->language()))
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setMjml')
            ->with($renderedMjmlTemplate)
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setPlain')
            ->with($renderedPlainTemplate)
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setSender')
            ->with($this->emailConfig->senderEmail(), $this->emailConfig->senderName())
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setFooterMail')
            ->with($this->emailConfig->senderEmail())
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('addToRecipient')
            ->with(
                $data->prospectEmail()->toString(),
                $data->prospectFirstName()?->toString().$data->prospectLastName()->toString()
            )
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setTrackingData')
            ->with(
                'Reservation',
                'deposit_payment_session_created',
                [
                    'reservation_id' => $data->reservationId()->toString(),
                    'prospect_id' => $data->prospectId()->toString(),
                    'unit_ids' => $data->unitCollection()->ids(),
                ],
            )
            ->willReturn($this->mailRendererClient);

        $this->mailRendererClient
            ->expects(self::once())
            ->method('send');

        $this->mailer->sendEmail($data);
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailData
     * @dataProvider provideMailerSendEmailData
     */
    public function testSendEmailCatchFailedToSendEmailException(
        array $depositPaymentEmailData,
        string $language
    ): void {
        $failedToSendEmailException = $this->getMockForAbstractClass(FailedToSendMailWithMailRendererException::class);

        $data = DepositPaymentEmailData::fromArray($depositPaymentEmailData);
        $renderedMjmlTemplate = View::make('mails.'.$language.'.deposit_payment_mjml', ['data' => $data])->render();
        $renderedPlainTemplate = View::make('mails.'.$language.'.deposit_payment_plain', ['data' => $data])->render();

        $this->mailRendererHelper
            ->expects(self::exactly(2))
            ->method('render')
            ->willReturnOnConsecutiveCalls(
                $renderedMjmlTemplate,
                $renderedPlainTemplate,
            );

        $this->mailTranslationHelper
            ->expects(self::once())
            ->method('setLocale')
            ->with($data->language()->toString());

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setSubject')
            ->with($this->emailConfig->getSubjectByLanguage($data->language()))
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setMjml')
            ->with($renderedMjmlTemplate)
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setPlain')
            ->with($renderedPlainTemplate)
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setSender')
            ->with($this->emailConfig->senderEmail(), $this->emailConfig->senderName())
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setFooterMail')
            ->with($this->emailConfig->senderEmail())
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('addToRecipient')
            ->with(
                $data->prospectEmail()->toString(),
                $data->prospectFirstName()?->toString().$data->prospectLastName()->toString()
            )
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setTrackingData')
            ->with(
                'Reservation',
                'deposit_payment_session_created',
                [
                    'reservation_id' => $data->reservationId()->toString(),
                    'prospect_id' => $data->prospectId()->toString(),
                    'unit_ids' => $data->unitCollection()->ids(),
                ],
            )
            ->willReturn($this->mailRendererClient);

        $this->mailRendererClient
            ->expects(self::once())
            ->method('send')
            ->willThrowException($failedToSendEmailException);

        $result = $this->mailer->sendEmail($data);

        self::assertEquals(
            null,
            $result,
            'result of send email method does not match expected null.'
        );
    }

    /**
     * @param array<string, mixed> $depositPaymentEmailData
     * @dataProvider provideMailerSendEmailData
     */
    public function testSendEmailCatchMissingFieldsInMailRendererRequestException(
        array $depositPaymentEmailData,
        string $language
    ): void {
        $missingFieldsRequestException = $this->getMockForAbstractClass(MissingFieldsInMailRendererRequestException::class);

        $data = DepositPaymentEmailData::fromArray($depositPaymentEmailData);
        $renderedMjmlTemplate = View::make('mails.'.$language.'.deposit_payment_mjml', ['data' => $data])->render();
        $renderedPlainTemplate = View::make('mails.'.$language.'.deposit_payment_plain', ['data' => $data])->render();

        $this->mailRendererHelper
            ->expects(self::exactly(2))
            ->method('render')
            ->willReturnOnConsecutiveCalls(
                $renderedMjmlTemplate,
                $renderedPlainTemplate,
            );

        $this->mailTranslationHelper
            ->expects(self::once())
            ->method('setLocale')
            ->with($data->language()->toString());

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setSubject')
            ->with($this->emailConfig->getSubjectByLanguage($data->language()))
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setMjml')
            ->with($renderedMjmlTemplate)
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setPlain')
            ->with($renderedPlainTemplate)
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setSender')
            ->with($this->emailConfig->senderEmail(), $this->emailConfig->senderName())
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setFooterMail')
            ->with($this->emailConfig->senderEmail())
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('addToRecipient')
            ->with(
                $data->prospectEmail()->toString(),
                $data->prospectFirstName()?->toString().$data->prospectLastName()->toString()
            )
            ->willReturnSelf();

        $this->mailRendererClient
            ->expects(self::once())
            ->method('setTrackingData')
            ->with(
                'Reservation',
                'deposit_payment_session_created',
                [
                    'reservation_id' => $data->reservationId()->toString(),
                    'prospect_id' => $data->prospectId()->toString(),
                    'unit_ids' => $data->unitCollection()->ids(),
                ],
            )
            ->willReturn($this->mailRendererClient);

        $this->mailRendererClient
            ->expects(self::once())
            ->method('send')
            ->willThrowException($missingFieldsRequestException);

        $result = $this->mailer->sendEmail($data);

        self::assertEquals(
            null,
            $result,
            'result of send email method does not match expected null.'
        );
    }

    public function provideMailerSendEmailData(): Generator
    {
        $language = 'en';
        yield 'Mailer send email data in english' => [
            'deposit payment email data array' => [
                DepositPaymentEmailData::RESERVATION_ID => '1234-1234-1234',
                DepositPaymentEmailData::LANGUAGE => $language,
                DepositPaymentEmailData::PROSPECT_ID => 'ca50819f-e5a4-40d3-a425-daba3e095407',
                DepositPaymentEmailData::PROSPECT_EMAIL => 'max.mustermann@gmail.com',
                DepositPaymentEmailData::PROSPECT_FIRST_NAME => 'Max',
                DepositPaymentEmailData::PROSPECT_LAST_NAME => 'Mustermann',
                DepositPaymentEmailData::PROSPECT_SALUTATION => 0,
                DepositPaymentEmailData::UNIT_COLLECTION => [
                    [
                        'id' => 1,
                        'deposit' => 3000.00,
                        'name' => 'WE 1',
                    ],
                ],
                DepositPaymentEmailData::CHECKOUT_SESSION_URL => 'https://example.com/',
                DepositPaymentEmailData::EXPIRES_AT => '2016-06-16T16:00:00.000000',
            ],
            'language' => $language,
        ];

        $language = 'de';
        yield 'Mailer send email data in german' => [
            'deposit payment email data array' => [
                DepositPaymentEmailData::RESERVATION_ID => '1234-1234-1234',
                DepositPaymentEmailData::LANGUAGE => $language,
                DepositPaymentEmailData::PROSPECT_ID => 'ca50819f-e5a4-40d3-a425-daba3e095407',
                DepositPaymentEmailData::PROSPECT_EMAIL => 'max.mustermann@gmail.com',
                DepositPaymentEmailData::PROSPECT_FIRST_NAME => 'Max',
                DepositPaymentEmailData::PROSPECT_LAST_NAME => 'Mustermann',
                DepositPaymentEmailData::PROSPECT_SALUTATION => 0,
                DepositPaymentEmailData::UNIT_COLLECTION => [
                    [
                        'id' => 1,
                        'deposit' => 3000.00,
                        'name' => 'WE 1',
                    ],
                ],
                DepositPaymentEmailData::CHECKOUT_SESSION_URL => 'https://example.com/',
                DepositPaymentEmailData::EXPIRES_AT => '2016-06-16T16:00:00.000000',
            ],
            'language' => $language,
        ];
    }
}
