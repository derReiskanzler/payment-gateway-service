<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Application\UseCase\SendDepositPaymentEmailToProspect\Command;

use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\Command\SendDepositPaymentEmailToProspect;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionUrl;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\ExpiresAt;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use PHPUnit\Framework\TestCase;

final class SendDepositPaymentEmailToProspectTest extends TestCase
{
    private SendDepositPaymentEmailToProspect $sendDepositPaymentEmailToProspect;
    private CommandId $commandId;
    private ReservationId $reservationId;
    private CheckoutSessionId $checkoutSessionId;
    private CheckoutSessionUrl $checkoutSessionUrl;
    private ExpiresAt $expiresAt;

    public function setUp(): void
    {
        parent::setUp();

        $this->commandId = CommandId::fromString('a7f55866-3f23-497e-991c-8ec1787a21d0');
        $this->reservationId = ReservationId::fromString('1234-1234-1234');
        $this->checkoutSessionId = CheckoutSessionId::fromString('cs_test_a1rRybd6FKpWXqCnXUolJf765O8sw0zL9U6eIn7YUIdTffmKiWwQzyTcI2');
        $this->checkoutSessionUrl = CheckoutSessionUrl::fromString('https://www.example.com');
        $this->expiresAt = ExpiresAt::fromSeconds(1653004800);

        $this->sendDepositPaymentEmailToProspect = new SendDepositPaymentEmailToProspect(
            $this->commandId,
            $this->reservationId,
            $this->checkoutSessionId,
            $this->checkoutSessionUrl,
            $this->expiresAt,
            [],
        );
    }

    public function testCommandName(): void
    {
        self::assertEquals(
            'PaymentGateway.SendDepositPaymentEmailToProspect',
            $this->sendDepositPaymentEmailToProspect->commandName()->toString(),
            'send deposit payment email to prospect command name does not match expected string.'
        );
    }

    public function testUuid(): void
    {
        self::assertEquals(
            $this->commandId->toString(),
            $this->sendDepositPaymentEmailToProspect->uuid()->toString(),
            'send deposit payment email to prospect command id does not match expected string.'
        );
    }

    public function testMetadata(): void
    {
        self::assertEquals(
            [],
            $this->sendDepositPaymentEmailToProspect->metadata(),
            'send deposit payment email to prospect command metadata does not match expected array.'
        );
    }

    public function testReservationId(): void
    {
        self::assertEquals(
            $this->reservationId->toString(),
            $this->sendDepositPaymentEmailToProspect->reservationId()->toString(),
            'send deposit payment email to prospect command reservation id does not match expected string.'
        );
    }

    public function testCheckoutSessionId(): void
    {
        self::assertEquals(
            $this->checkoutSessionId->toString(),
            $this->sendDepositPaymentEmailToProspect->checkoutSessionId()->toString(),
            'send deposit payment email to prospect command checkout session id does not match expected string.'
        );
    }

    public function testCheckoutSessionUrl(): void
    {
        self::assertEquals(
            $this->checkoutSessionUrl->toString(),
            $this->sendDepositPaymentEmailToProspect->checkoutSessionUrl()->toString(),
            'send deposit payment email to prospect command checkout session url does not match expected string.'
        );
    }

    public function testExpiresAt(): void
    {
        self::assertEquals(
            $this->expiresAt->toString(),
            $this->sendDepositPaymentEmailToProspect->expiresAt()->toString(),
            'send deposit payment email to prospect command expires at does not match expected string.'
        );
    }
}
