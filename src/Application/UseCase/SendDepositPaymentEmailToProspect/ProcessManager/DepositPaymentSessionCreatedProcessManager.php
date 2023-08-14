<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\ProcessManager;

use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command\SendDepositPaymentEmailToProspect;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command\SendDepositPaymentEmailToProspectHandlerInterface;
use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\EventProjections\Contracts\EventHandlers\EventHandlerInterface;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGeneratorInterface;

final class DepositPaymentSessionCreatedProcessManager implements EventHandlerInterface
{
    private const PAYMENT_GATEWAY_DEPOSIT_PAYMENT_SESSION_CREATED = Context::NAME.'DepositPaymentSessionCreated';

    public function __construct(
        private CommandIdGeneratorInterface $commandIdGenerator,
        private SendDepositPaymentEmailToProspectHandlerInterface $sendDepositPaymentEmailToProspectHandler,
    ) {
    }

    public function handle(EventDTO $event): void
    {
        switch ($event->getName()) {
            case self::PAYMENT_GATEWAY_DEPOSIT_PAYMENT_SESSION_CREATED:
                $this->handleDepositPaymentSessionCreated($event);
                break;
            default:
                break;
        }
    }

    private function handleDepositPaymentSessionCreated(EventDTO $event): void
    {
        $payload = $event->getPayload();
        $this->sendDepositPaymentEmailToProspectHandler->handle(
            new SendDepositPaymentEmailToProspect(
                $this->commandIdGenerator->generate(),
                ReservationId::fromString($payload['reservation_id']),
                CheckoutSessionId::fromString($payload['checkout_session_id']),
                CheckoutSessionUrl::fromString($payload['checkout_session_url']),
                ExpiresAt::fromString($payload['expires_at']),
            )
        );
    }
}
