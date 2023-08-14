<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\InvalidStreamNameFormat;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;

final class ContextStreamName
{
    use toString;

    public static function fromString(string $streamName): self
    {
        return new self($streamName);
    }

    /**
     * @param string $streamName Stream Name
     */
    private static function validateStreamName(string $streamName): void
    {
        if (strtolower($streamName) !== $streamName) {
            throw InvalidStreamNameFormat::notLowerCase($streamName);
        }
    }

    /**
     * @throws InvalidStreamNameFormat if $streamName is not lowercase
     */
    private function __construct(
        private string $streamName
    ) {
        self::validateStreamName($streamName);
    }

    public function toString(): string
    {
        return $this->streamName;
    }
}
