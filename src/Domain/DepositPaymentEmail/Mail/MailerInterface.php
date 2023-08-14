<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentEmail\Mail;

use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\DepositPaymentEmailData;
use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\RequestId;

interface MailerInterface
{
    public function sendEmail(DepositPaymentEmailData $emailData): ?RequestId;
}
