<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Exceptions\InvalidEventNameFormat;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;
use function explode;

final class EventName
{
    use toString;

    private string $context;

    private string $internalName;

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    /**
     * @return array<string>
     */
    private static function split(string $eventName): array
    {
        $parts = explode('.', $eventName);

        if (2 !== \count($parts)) {
            throw InvalidEventNameFormat::notDotSeparated($eventName);
        }

        return $parts;
    }

    private function __construct(string $name)
    {
        [$this->context, $this->internalName] = self::split($name);
    }

    public function toString(): string
    {
        return $this->context.'.'.$this->internalName;
    }
}
