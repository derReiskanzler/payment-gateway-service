<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Controller;

use Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Command\CompleteDepositPaymentSession;
use Allmyhomes\Application\UseCase\CompleteDepositPaymentSession\Command\CompleteDepositPaymentSessionHandlerInterface;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionId;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\CheckoutSessionStatus;
use Allmyhomes\Domain\DepositPaymentSession\ValueObject\PaymentStatus;
use Allmyhomes\Domain\ValueObject\ReservationId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGeneratorInterface;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Traits\ResponseFormatTrait;
use Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Exception\WebhookJsonContentIsNotAStringException;
use Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Exception\WebhookSignatureIsNotAStringException;
use Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Exception\WebhookSignatureVerificationIsNotValidException;
use Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Request\CompleteDepositPaymentSessionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class CompleteDepositPaymentSessionController extends Controller
{
    use ResponseFormatTrait;

    public function __construct(
        private CommandIdGeneratorInterface $commandIdGenerator,
        private CompleteDepositPaymentSessionHandlerInterface $completeDepositPaymentSessionHandler,
    ) {
    }

    /**
     * @throws WebhookJsonContentIsNotAStringException
     * @throws WebhookSignatureIsNotAStringException
     * @throws WebhookSignatureVerificationIsNotValidException
     */
    public function __invoke(CompleteDepositPaymentSessionRequest $request): JsonResponse
    {
        $this->completeDepositPaymentSessionHandler->handle(
            new CompleteDepositPaymentSession(
                $this->commandIdGenerator->generate(),
                ReservationId::fromString($request->reservationId()),
                CheckoutSessionId::fromString($request->checkoutSessionId()),
                CheckoutSessionStatus::fromString($request->checkoutSessionStatus()),
                PaymentStatus::fromString($request->paymentStatus()),
            )
        );

        return $this->response->noContent();
    }
}
