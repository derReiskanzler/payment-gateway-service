<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\RetryDepositPaymentSessionCreation\Command;

use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command\RetryDepositPaymentSessionCreation;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use PHPUnit\Framework\TestCase;

final class RetryDepositPaymentSessionCreationTest extends TestCase
{
    private RetryDepositPaymentSessionCreation $retryDepositPaymentSessionCreation;
    private CommandId $commandId;
    private ReservationId $reservationId;
    private ErrorCount $errorCount;

    public function setUp(): void
    {
        parent::setUp();

        $this->commandId = CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0');
        $this->reservationId = ReservationId::fromString('1234-1234-1234');
        $this->errorCount = ErrorCount::fromInt(1);

        $this->retryDepositPaymentSessionCreation = new RetryDepositPaymentSessionCreation(
            $this->commandId,
            $this->reservationId,
            $this->errorCount,
            [],
        );
    }

    public function testCommandName(): void
    {
        self::assertEquals(
            'PaymentGateway.RetryDepositPaymentSessionCreation',
            $this->retryDepositPaymentSessionCreation->commandName()->toString(),
            'create deposit payment session command name does not match expected command name.'
        );
    }

    public function testUuid(): void
    {
        self::assertEquals(
            $this->commandId->toString(),
            $this->retryDepositPaymentSessionCreation->uuid()->toString(),
            'create deposit payment session command id does not match expected command id.'
        );
    }

    public function testMetadata(): void
    {
        self::assertEquals(
            [],
            $this->retryDepositPaymentSessionCreation->metadata(),
            'create deposit payment session command metadata does not match expected metadata.'
        );
    }

    public function testReservationId(): void
    {
        self::assertEquals(
            $this->reservationId->toString(),
            $this->retryDepositPaymentSessionCreation->reservationId()->toString(),
            'create deposit payment session command reservation id does not match expected reservation id.'
        );
    }

    public function testErrorCount(): void
    {
        self::assertEquals(
            $this->errorCount->toInt(),
            $this->retryDepositPaymentSessionCreation->errorCount()->toInt(),
            'create deposit payment session command error count does not match expected error count.'
        );
    }
}
