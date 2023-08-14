<?php

declare(strict_types=1);

namespace Tests\doubles\EventSourcing;

use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class UserIdDouble
{
    private UuidInterface $userId;

    /**
     * @throws Exception
     *
     * @return static
     */
    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    /**
     * @return static
     */
    public static function fromString(string $userId): self
    {
        return new self(Uuid::fromString($userId));
    }

    /**
     * UserIdDouble constructor.
     */
    private function __construct(UuidInterface $userId)
    {
        $this->userId = $userId;
    }

    public function toString(): string
    {
        return $this->userId->toString();
    }

    /**
     * @param mixed $other Other VO
     */
    public function equals(mixed $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->userId->equals($other->userId);
    }

    public function __toString(): string
    {
        return $this->userId->toString();
    }
}
