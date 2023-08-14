<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\CreateDepositPaymentSession\ProcessManager;

use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command\CreateDepositPaymentSession;
use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command\CreateDepositPaymentSessionHandlerInterface;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\EventProjections\Contracts\EventHandlers\EventHandlerInterface;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGeneratorInterface;

final class ReservationAcceptedByInventoryProcessManager implements EventHandlerInterface
{
    private const RESERVATION_MANAGEMENT_RESERVATION_ACCEPTED_BY_INVENTORY = 'ReservationManagement.ReservationAcceptedByInventory';

    public function __construct(
        private CommandIdGeneratorInterface $commandIdGenerator,
        private CreateDepositPaymentSessionHandlerInterface $createDepositPaymentSessionHandler
    ) {
    }

    public function handle(EventDTO $event): void
    {
        switch ($event->getName()) {
            case self::RESERVATION_MANAGEMENT_RESERVATION_ACCEPTED_BY_INVENTORY:
                $this->handleReservationAcceptedByInventory($event);
                break;
            default:
                break;
        }
    }

    private function handleReservationAcceptedByInventory(EventDTO $event): void
    {
        $payload = $event->getPayload();
        $this->createDepositPaymentSessionHandler->handle(
            new CreateDepositPaymentSession(
                $this->commandIdGenerator->generate(),
                ReservationId::fromString($payload['reservation_id']),
            )
        );
    }
}
