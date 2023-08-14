<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command;

interface CreateDepositPaymentSessionHandlerInterface
{
    public function handle(CreateDepositPaymentSession $command): void;
}
