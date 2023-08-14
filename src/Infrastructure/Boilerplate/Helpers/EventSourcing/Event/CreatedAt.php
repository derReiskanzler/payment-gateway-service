<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventSourcing\Event;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\toString;
use DateTimeImmutable;
use DateTimeZone;
use UnexpectedValueException;

final class CreatedAt
{
    use toString;

    public const FORMAT = 'Y-m-d\TH:i:s.u';
    public const TIMEZONE = 'UTC';

    private DateTimeImmutable $createdAt;

    public static function now(): self
    {
        return self::fromDateTime(new DateTimeImmutable());
    }

    public static function fromDateTime(DateTimeImmutable $createdAt): self
    {
        $createdAt = self::ensureCorrectTimezone($createdAt);

        return new self($createdAt);
    }

    public static function fromString(string $createdAt): self
    {
        if (19 === \strlen($createdAt)) {
            $createdAt .= '.000';
        }

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $createdAt = DateTimeImmutable::createFromFormat(
            self::FORMAT,
            $createdAt,
            new DateTimeZone('UTC')
        );

        if (false === $createdAt) {
            throw new UnexpectedValueException('Invalid createdAt provided');
        }

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $createdAt = self::ensureCorrectTimezone($createdAt);

        return new self($createdAt);
    }

    private static function ensureCorrectTimezone(DateTimeImmutable $createdAt): DateTimeImmutable
    {
        if (self::TIMEZONE !== $createdAt->getTimezone()->getName()) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $createdAt = $createdAt->setTimezone(new DateTimeZone(self::TIMEZONE));
        }

        return $createdAt;
    }

    private function __construct(DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function toString(): string
    {
        return $this->createdAt->format(self::FORMAT);
    }

    public function dateTime(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
