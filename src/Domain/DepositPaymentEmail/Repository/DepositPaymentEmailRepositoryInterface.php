<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentEmail\Repository;

use Allmyhomes\Domain\DepositPaymentEmail\Aggregate\DepositPaymentEmail;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;

interface DepositPaymentEmailRepositoryInterface
{
    public function save(DepositPaymentEmail $depositPaymentEmail, Command $command): void;
}
