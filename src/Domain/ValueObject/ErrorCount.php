<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\ValueObject;

use Allmyhomes\Domain\Context;

final class ErrorCount
{
    public static function fromInt(int $errorCount): self
    {
        return new self($errorCount);
    }

    private function __construct(private int $errorCount)
    {
    }

    public function toInt(): int
    {
        return $this->errorCount;
    }

    public function increase(): self
    {
        return new self(++$this->errorCount);
    }

    public function exceedsMaxErrorCount(): bool
    {
        return $this->errorCount > Context::MAX_RETRY_ATTEMPTS;
    }
}
