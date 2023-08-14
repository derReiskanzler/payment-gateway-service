<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Doubles;

use Allmyhomes\Infrastructure\Util\ExternalApi\StripeSignatureCheckServiceInterface;

final class StripeSignatureCheckServiceDouble implements StripeSignatureCheckServiceInterface
{
    public function __construct(
        private bool $shouldFail = false,
    ) {
    }

    public function hasValidSignature(string $jsonContent, string $signature, ): bool|string
    {
        if ($this->shouldFail) {
            return 'expected error thrown';
        }

        return true;
    }
}
