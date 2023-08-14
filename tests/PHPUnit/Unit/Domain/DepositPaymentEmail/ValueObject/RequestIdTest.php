<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\DepositPaymentEmail\ValueObject;

use Allmyhomes\Domain\DepositPaymentEmail\ValueObject\RequestId;
use PHPUnit\Framework\TestCase;

final class RequestIdTest extends TestCase
{
    public function testFromString(): void
    {
        $requestId = RequestId::fromString('request id');

        $this->assertInstanceOf(
            RequestId::class,
            $requestId,
            'created request id from string is not instance of expected class: RequestId.'
        );
    }

    public function testToString(): void
    {
        $string = 'request id';
        $requestId = RequestId::fromString($string);

        $this->assertEquals(
            $string,
            $requestId->toString(),
            'created request id from string does not match expected string.',
        );
    }
}
