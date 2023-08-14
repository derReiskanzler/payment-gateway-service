<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\CreateDepositPaymentSession\Command;

use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\Command\CreateDepositPaymentSession;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use PHPUnit\Framework\TestCase;

final class CreateDepositPaymentSessionTest extends TestCase
{
    private CreateDepositPaymentSession $createDepositPaymentSession;
    private CommandId $commandId;
    private ReservationId $reservationId;

    public function setUp(): void
    {
        parent::setUp();

        $this->commandId = CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0');
        $this->reservationId = ReservationId::fromString('1234-1234-1234');

        $this->createDepositPaymentSession = new CreateDepositPaymentSession(
            $this->commandId,
            $this->reservationId,
            [],
        );
    }

    public function testCommandName(): void
    {
        self::assertEquals(
            'PaymentGateway.CreateDepositPaymentSession',
            $this->createDepositPaymentSession->commandName(),
            'create deposit payment session command name does not match expected command name.'
        );
    }

    public function testUuid(): void
    {
        self::assertEquals(
            $this->commandId,
            $this->createDepositPaymentSession->uuid(),
            'create deposit payment session command id does not match expected command id.'
        );
    }

    public function testMetadata(): void
    {
        self::assertEquals(
            [],
            $this->createDepositPaymentSession->metadata(),
            'create deposit payment session command metadata does not match expected metadata.'
        );
    }

    public function testReservationId(): void
    {
        self::assertEquals(
            $this->reservationId,
            $this->createDepositPaymentSession->reservationId(),
            'create deposit payment session command reservation id does not match expected reservation id.'
        );
    }
}
