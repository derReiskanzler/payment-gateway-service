<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command;

interface SendDepositPaymentEmailToProspectHandlerInterface
{
    public function handle(SendDepositPaymentEmailToProspect $command): void;
}
