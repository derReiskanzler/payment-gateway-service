<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandName;

final class RetryDepositPaymentSessionCreation implements Command
{
    private const COMMAND_NAME = Context::NAME.'RetryDepositPaymentSessionCreation';

    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private CommandId $commandId,
        private ReservationId $reservationId,
        private ErrorCount $errorCount,
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

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function reservationId(): ReservationId
    {
        return $this->reservationId;
    }

    public function errorCount(): ErrorCount
    {
        return $this->errorCount;
    }
}
