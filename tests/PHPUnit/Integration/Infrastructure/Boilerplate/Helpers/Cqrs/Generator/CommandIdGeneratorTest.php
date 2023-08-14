<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Helpers\Cqrs\Generator;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Generator\CommandIdGeneratorInterface;
use Tests\TestCase;

final class CommandIdGeneratorTest extends TestCase
{
    /**
     * @testdox CommandIdGenerator could generate a commandId with integration of Ramsey Uuid
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testGenerate(): void
    {
        $commandIdGenerator = app()->make(CommandIdGeneratorInterface::class);

        $commandId = $commandIdGenerator->generate();

        self::assertInstanceOf(CommandId::class, $commandId);
    }
}
