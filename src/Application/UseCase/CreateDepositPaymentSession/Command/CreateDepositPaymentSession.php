<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandName;

final class CreateDepositPaymentSession implements Command
{
    private const COMMAND_NAME = Context::NAME.'CreateDepositPaymentSession';

    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private CommandId $commandId,
        private ReservationId $reservationId,
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
}
