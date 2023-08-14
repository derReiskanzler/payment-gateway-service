<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\Repository;

use Allmyhomes\Domain\DepositPaymentSession\Aggregate\DepositPaymentSession;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;

interface DepositPaymentSessionRepositoryInterface
{
    public function save(DepositPaymentSession $depositPaymentSession, Command $command): void;

    public function getById(ReservationId $id): DepositPaymentSession|null;
}
