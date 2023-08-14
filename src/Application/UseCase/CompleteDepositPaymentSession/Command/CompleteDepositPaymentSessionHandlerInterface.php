<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Command;

interface CompleteDepositPaymentSessionHandlerInterface
{
    public function handle(CompleteDepositPaymentSession $command): void;
}
