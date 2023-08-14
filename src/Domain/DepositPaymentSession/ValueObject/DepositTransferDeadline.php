<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\DepositPaymentSession\ValueObject;

use Allmyhomes\Domain\Context;
use Allmyhomes\Domain\Timezone;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use InvalidArgumentException;

final class DepositTransferDeadline
{
    public static function fromDateTime(DateTimeImmutable $dateTime): self
    {
        return new self(self::ensureUtc($dateTime));
    }

    /**
     * @param int $seconds epoch time - number of seconds that have elapsed since January 1, 1970
     *
     * @return static
     */
    public static function fromSeconds(int $seconds): self
    {
        $dateTimeImmutable = (new \DateTimeImmutable())->setTimestamp($seconds);

        $dateTimeImmutable = self::ensureUtc($dateTimeImmutable);

        return new self($dateTimeImmutable);
    }

    public static function fromString(string $dateTime): self
    {
        try {
            $dateTimeImmutable = new DateTimeImmutable($dateTime);
        } catch (Exception $e) {
            throw new InvalidArgumentException(sprintf('String "%s" is not supported. Use a date time format which is compatible with ISO 8601.', $dateTime), $e->getCode(), $e);
        }

        $dateTimeImmutable = self::ensureUtc($dateTimeImmutable);

        return new self($dateTimeImmutable);
    }

    private function __construct(private DateTimeImmutable $dateTime)
    {
    }

    public function dateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    /**
     * @return int epoch time - number of seconds that have elapsed since January 1, 1970
     */
    public function toSeconds(): int
    {
        return $this->dateTime->getTimestamp();
    }

    public function toString(): string
    {
        return $this->dateTime->format(Context::DEFAULT_TIME_FORMAT);
    }

    private static function ensureUtc(DateTimeImmutable $dateTime): DateTimeImmutable
    {
        if (Timezone::UTC !== $dateTime->getTimezone()->getName()) {
            $dateTime = $dateTime->setTimezone(new DateTimeZone(Timezone::UTC));
        }

        return $dateTime;
    }
}
