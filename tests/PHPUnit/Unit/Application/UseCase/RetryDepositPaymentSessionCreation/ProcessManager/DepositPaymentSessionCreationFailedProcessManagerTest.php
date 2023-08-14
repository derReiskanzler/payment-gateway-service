<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\RetryDepositPaymentSessionCreation\ProcessManager;

use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command\RetryDepositPaymentSessionCreation;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\Command\RetryDepositPaymentSessionCreationHandlerInterface;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\ProcessManager\DepositPaymentSessionCreationFailedProcessManager;
use Allmyhomes\Domain\ValueObject\ErrorCount;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\EventProjections\Services\EventHandlers\EventDTO;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGeneratorInterface;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DepositPaymentSessionCreationFailedProcessManagerTest extends TestCase
{
    private DepositPaymentSessionCreationFailedProcessManager $processManager;
    /**
     * @var CommandIdGeneratorInterface&MockObject
     */
    private CommandIdGeneratorInterface $commandIdGenerator;

    /**
     * @var RetryDepositPaymentSessionCreationHandlerInterface&MockObject
     */
    private RetryDepositPaymentSessionCreationHandlerInterface $retryDepositPaymentSessionCreationSessionHandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->commandIdGenerator = $this->createMock(CommandIdGeneratorInterface::class);
        $this->retryDepositPaymentSessionCreationSessionHandler = $this->createMock(RetryDepositPaymentSessionCreationHandlerInterface::class);

        $this->processManager = new DepositPaymentSessionCreationFailedProcessManager(
            $this->commandIdGenerator,
            $this->retryDepositPaymentSessionCreationSessionHandler,
        );
    }

    /**
     * @dataProvider provideReservationEvents
     */
    public function testHandle(EventDTO $event, RetryDepositPaymentSessionCreation $command): void
    {
        $this->retryDepositPaymentSessionCreationSessionHandler
            ->expects(self::once())
            ->method('handle')
            ->with($command);

        $this->commandIdGenerator
            ->expects(self::once())
            ->method('generate')
            ->willReturn($command->uuid());

        $this->processManager->handle($event);
    }

    /**
     * @dataProvider provideOtherEvent
     */
    public function testHandleOtherEvent(EventDTO $event): void
    {
        $this->retryDepositPaymentSessionCreationSessionHandler
            ->expects($this->never())
            ->method('handle');

        $this->commandIdGenerator
            ->expects($this->never())
            ->method('generate');

        $this->processManager->handle($event);
    }

    public function provideReservationEvents(): Generator
    {
        $reservationId = '1111-2222-3333';
        $errorCount = 1;

        yield 'ReservationAcceptedByInventory Event with full payload' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'PaymentGateway.DepositPaymentSessionCreationFailed',
                [
                    'reservation_id' => $reservationId,
                    'error_count' => $errorCount,
                ],
                []
            ),
            new RetryDepositPaymentSessionCreation(
                CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0'),
                ReservationId::fromString($reservationId),
                ErrorCount::fromInt($errorCount),
            ),
        ];
    }

    /**
     * @return Generator<mixed>
     */
    public function provideOtherEvent(): Generator
    {
        yield 'Other Event with empty payload' => [
            new EventDTO(
                'de174fab-a83d-4094-bc2d-ee7cd8407813',
                'Other.Event',
                [],
                []
            ),
        ];
    }
}
