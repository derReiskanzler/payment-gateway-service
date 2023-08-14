<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Command;

use Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Exception\DepositPaymentSessionNotFoundException;
use Allmyhomes\Domain\DepositPaymentSession\Repository\DepositPaymentSessionRepositoryInterface;

final class CompleteDepositPaymentSessionHandler implements CompleteDepositPaymentSessionHandlerInterface
{
    public function __construct(
        private DepositPaymentSessionRepositoryInterface $depositPaymentSessionRepository,
    ) {
    }

    public function handle(CompleteDepositPaymentSession $command): void
    {
        $depositPaymentSessionAggregate = $this->depositPaymentSessionRepository->getById($command->reservationId());

        if (empty($depositPaymentSessionAggregate)) {
            throw DepositPaymentSessionNotFoundException::forReservationId($command->reservationId());
        }

        $depositPaymentSessionAggregate->completeDepositPaymentSession(
            $command->checkoutSessionId(),
            $command->checkoutSessionStatus(),
            $command->paymentStatus()
        );

        $this->depositPaymentSessionRepository->save($depositPaymentSessionAggregate, $command);
    }
}
