<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\AgentId;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class AgentIdTest extends TestCase
{
    public function testFromString(): void
    {
        $agentId = AgentId::fromString('da7c58f5-4c74-4722-8b94-7fcf8d857055');

        $this->assertInstanceOf(
            AgentId::class,
            $agentId,
            'created agent id from string is not instance of expected class: AgentId.'
        );
    }

    public function testFromStringWithInvalidUuid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AgentId::fromString('no uuid');
    }

    public function testToString(): void
    {
        $string = 'da7c58f5-4c74-4722-8b94-7fcf8d857055';
        $agentId = AgentId::fromString($string);

        $this->assertEquals(
            $string,
            $agentId->toString(),
            'created agent id from string does not match expected string.',
        );
    }
}
