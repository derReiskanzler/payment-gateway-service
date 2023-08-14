<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\ProcessManager;

use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command\RetryDepositPaymentSessionCreation;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command\RetryDepositPaymentSessionCreationHandlerInterface;
use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\EventProjections\Contracts\EventHandlers\EventHandlerInterface;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGeneratorInterface;

final class DepositPaymentSessionCreationFailedProcessManager implements EventHandlerInterface
{
    private const PAYMENT_GATEWAY_DEPOSIT_PAYMENT_SESSION_CREATION_FAILED = Context::NAME.'DepositPaymentSessionCreationFailed';

    public function __construct(
        private CommandIdGeneratorInterface $commandIdGenerator,
        private RetryDepositPaymentSessionCreationHandlerInterface $retryDepositPaymentSessionCreationHandler,
    ) {
    }

    public function handle(EventDTO $event): void
    {
        switch ($event->getName()) {
            case self::PAYMENT_GATEWAY_DEPOSIT_PAYMENT_SESSION_CREATION_FAILED:
                $this->handleDepositPaymentSessionCreationFailed($event);
                break;
            default:
                break;
        }
    }

    private function handleDepositPaymentSessionCreationFailed(EventDTO $event): void
    {
        $payload = $event->getPayload();
        $this->retryDepositPaymentSessionCreationHandler->handle(
            new RetryDepositPaymentSessionCreation(
                $this->commandIdGenerator->generate(),
                ReservationId::fromString($payload['reservation_id']),
                ErrorCount::fromInt($payload['error_count']),
            )
        );
    }
}
