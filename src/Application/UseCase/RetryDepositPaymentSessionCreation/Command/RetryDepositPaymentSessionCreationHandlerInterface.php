<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command;

interface RetryDepositPaymentSessionCreationHandlerInterface
{
    public function handle(RetryDepositPaymentSessionCreation $command): void;
}
