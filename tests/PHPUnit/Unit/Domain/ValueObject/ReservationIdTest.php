<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\ReservationId;
use PHPUnit\Framework\TestCase;

final class ReservationIdTest extends TestCase
{
    public function testFromString(): void
    {
        $string = '1111-4c74-4722-8b94-7fcf8d857055';
        $reservationId = ReservationId::fromString($string);

        $this->assertInstanceOf(
            ReservationId::class,
            $reservationId,
            'created reservation id from string is not instance of expected class: ReservationId.'
        );
    }

    public function testToString(): void
    {
        $string = '1111-4c74-4722-8b94-7fcf8d857055';
        $reservationId = ReservationId::fromString($string);

        $this->assertEquals(
            $string,
            $reservationId->toString(),
            'created reservation id from string does not match expected string.',
        );
    }
}
