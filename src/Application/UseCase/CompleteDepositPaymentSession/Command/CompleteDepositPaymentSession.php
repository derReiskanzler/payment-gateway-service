<?php

declare(strict_types=1);

namespace Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Command;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandName;

final class CompleteDepositPaymentSession implements Command
{
    private const COMMAND_NAME = Context::NAME.'CompleteDepositPaymentSession';

    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private CommandId $commandId,
        private ReservationId $reservationId,
        private CheckoutSessionId $checkoutSessionId,
        private CheckoutSessionStatus $checkoutSessionStatus,
        private PaymentStatus $paymentStatus,
        private array $metadata = [],
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

    public function checkoutSessionId(): CheckoutSessionId
    {
        return $this->checkoutSessionId;
    }

    public function checkoutSessionStatus(): CheckoutSessionStatus
    {
        return $this->checkoutSessionStatus;
    }

    public function paymentStatus(): PaymentStatus
    {
        return $this->paymentStatus;
    }
}
