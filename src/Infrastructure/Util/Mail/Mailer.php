<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\Mail;

use Allmyhomes\Domain\DepositPaymentEmail\Mail\MailerInterface;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\DepositPaymentEmailData;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\RequestId;
use Allmyhomes\Infrastructure\Config\MailerConfig;
use Allmyhomes\Infrastructure\Util\Mail\Helper\MailRendererHelperInterface;
use Allmyhomes\Infrastructure\Util\Mail\Helper\MailTranslationHelperInterface;
use Allmyhomes\MailRendererClient\Exceptions\FailedToSendMailWithMailRendererException;
use Allmyhomes\MailRendererClient\Exceptions\MissingFieldsInMailRendererRequestException;
use Allmyhomes\MailRendererClient\Services\MailRendererClient;
use Psr\Log\LoggerInterface;

final class Mailer implements MailerInterface
{
    public function __construct(
        private MailRendererClient $mailRendererClient,
        private MailTranslationHelperInterface $mailTranslationHelper,
        private MailRendererHelperInterface $mailRendererHelper,
        private EmailConfig $emailConfig,
        private LoggerInterface $logger,
    ) {
    }

    private function render(string $type, object $data): string
    {
        $path = sprintf('mails.%s.%s_%s', $this->mailTranslationHelper->getLocale(), MailerConfig::EMAIL_TEMPLATE_FILENAME, $type);

        return $this->mailRendererHelper->render($path, [
            'data' => $data,
        ]);
    }

    public function sendEmail(DepositPaymentEmailData $emailData): ?RequestId
    {
        $this->mailTranslationHelper->setLocale($emailData->language()->toString());
        $email = $this->mailRendererClient
            ->setSubject($this->emailConfig->getSubjectByLanguage($emailData->language()))
            ->setMjml($this->render(MailerConfig::TYPE_MJML, $emailData))
            ->setPlain($this->render(MailerConfig::TYPE_PLAIN, $emailData))
            ->setSender($this->emailConfig->senderEmail(), $this->emailConfig->senderName())
            ->setFooterMail($this->emailConfig->senderEmail())
            ->addToRecipient(
                $emailData->prospectEmail()->toString(),
                $emailData->prospectFirstName()?->toString().$emailData->prospectLastName()->toString()
            )
            ->setTrackingData(
                MailerConfig::TRACKED_CONTEXT,
                MailerConfig::TEMPLATE_IDENTIFIER,
                [
                    MailerConfig::TRACKED_DATA_RESERVATION_ID => $emailData->reservationId()->toString(),
                    MailerConfig::TRACKED_DATA_PROSPECT_ID => $emailData->prospectId()->toString(),
                    MailerConfig::TRACKED_DATA_UNIT_IDS => $emailData->unitCollection()->ids(),
                ],
            );

        try {
            return RequestId::fromString($email->send());
        } catch (FailedToSendMailWithMailRendererException|MissingFieldsInMailRendererRequestException $e) {
            $this->logger->error($e->getMessage());

            return null;
        }
    }
}
