<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\Cqrs\Generator;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;

final class CommandIdGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $id = 'e817ffb0-4016-4325-931a-aa7d74166ce8';
        /** @var UuidFactoryInterface|MockObject $mockedUuidFactory */
        $mockedUuidFactory = $this->createMock(UuidFactoryInterface::class);
        $mockedUuidFactory->method('uuid4')
            ->willReturn(Uuid::fromString($id));
        $commandIdGenerator = new CommandIdGenerator($mockedUuidFactory);

        $commandId = $commandIdGenerator->generate();

        self::assertSame($id, $commandId->toString());
    }
}
