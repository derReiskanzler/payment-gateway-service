<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\ExternalApi;

interface StripeSignatureCheckServiceInterface
{
    public function hasValidSignature(
        string $jsonContent,
        string $signature,
    ): bool|string;
}
