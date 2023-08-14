<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Doubles;

use Allmyhomes\Domain\DepositPaymentEmail\Mail\MailerInterface;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\DepositPaymentEmailData;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\RequestId;

final class MailerDouble implements MailerInterface
{
    public function __construct(
        private string $requestId,
        private bool $shouldFail = false,
    ) {
    }

    public function sendEmail(DepositPaymentEmailData $emailData): ?RequestId
    {
        if ($this->shouldFail) {
            return null;
        }

        return RequestId::fromString($this->requestId);
    }
}
