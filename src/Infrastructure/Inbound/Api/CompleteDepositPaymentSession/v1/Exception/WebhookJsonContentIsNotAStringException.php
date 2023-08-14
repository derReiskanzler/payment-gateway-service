<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Exception;

use LogicException;
use Throwable;

final class WebhookJsonContentIsNotAStringException extends LogicException
{
    public function __construct(
        string $message = 'stripe webhook json content is not a string.',
        int $code = 400,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
