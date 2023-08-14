<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandName;

final class SendDepositPaymentEmailToProspect implements Command
{
    private const COMMAND_NAME = Context::NAME.'SendDepositPaymentEmailToProspect';

    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private CommandId $commandId,
        private ReservationId $reservationId,
        private CheckoutSessionId $checkoutSessionId,
        private CheckoutSessionUrl $checkoutSessionUrl,
        private ExpiresAt $expiresAt,
        private array $metadata = []
    ) {
    }

    public function commandName(): CommandName
    {
        return CommandName::fromString(self::COMMAND_NAME);
    }

    public function uuid(): CommandId
    {
        return $this->commandId;
    }

    public function reservationId(): ReservationId
    {
        return $this->reservationId;
    }

    public function checkoutSessionId(): CheckoutSessionId
    {
        return $this->checkoutSessionId;
    }

    public function checkoutSessionUrl(): CheckoutSessionUrl
    {
        return $this->checkoutSessionUrl;
    }

    public function expiresAt(): ExpiresAt
    {
        return $this->expiresAt;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }
}
