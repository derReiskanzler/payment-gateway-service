<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Laravel\App\Http\Controllers;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandHandler;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\CommandId;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Exceptions\CommandMetadataKeyMissing;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Exceptions\CommandPayloadKeyMissing;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Controllers\EventSourcingController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Tests\doubles\CommandDouble;
use Tests\doubles\CommandHandlerDouble;

class EventSourcingControllerTest extends TestCase
{
    private EventSourcingController $baseController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseController = new EventSourcingController();
    }

    public function testGetCommandWithoutMetadata(): void
    {
        $payload = ['room_size' => '20m'];
        $request = Request::create(
            '/v1/buyer/1/update-buyer-search-profile',
            'POST',
            $payload
        );

        $command = $this->reflectMethod($this->baseController, 'getCommand', [CommandDouble::class, $request]);

        static::assertInstanceOf(Command::class, $command);
        static::assertInstanceOf(CommandDouble::class, $command);
        static::assertSame('20m', $command->getRoomSize());
        static::assertSame(CommandDouble::APARTMENT_SIZE, $command->getApartmentSize());
        static::assertSame([], $command->metadata());
    }

    public function testGetCommandWithMetadata(): void
    {
        $metadata = ['requester' => 'boilerplate'];
        $payload = ['room_size' => '20m'];
        $request = Request::create(
            '/v1/buyer/1/update-buyer-search-profile',
            'POST',
            $payload
        );

        $command = $this->reflectMethod($this->baseController, 'getCommand', [CommandDouble::class, $request, $metadata]);

        static::assertInstanceOf(CommandDouble::class, $command);
        static::assertSame('20m', $command->getRoomSize());
        static::assertSame($metadata, $command->metadata());
        static::assertSame('boilerplate', $command->getMeta('requester'));
        static::assertSame('boilerplate_default', $command->getMetaOrDefault('invalid', 'boilerplate_default'));
    }

    public function testGetCommandWithCommandId(): void
    {
        $payload = ['room_size' => '20m'];
        $commandId = CommandId::fromString('a5fa93d7-c7c6-479c-81ed-fcf7e7f7b1c5');
        $request = Request::create(
            '/v1/buyer/1/update-buyer-search-profile',
            'POST',
            $payload
        );

        $command = $this->reflectMethod($this->baseController, 'getCommand', [CommandDouble::class, $request, [], $commandId]);

        static::assertInstanceOf(CommandDouble::class, $command);
        static::assertSame('20m', $command->getRoomSize());
        static::assertSame('a5fa93d7-c7c6-479c-81ed-fcf7e7f7b1c5', $command->uuid()->toString());
    }

    public function testGetCommandWithInvalidPayloadKey(): void
    {
        $metadata = ['requester' => 'boilerplate'];
        $payload = ['room_size' => '20m'];
        $request = Request::create(
            '/v1/buyer/1/update-buyer-search-profile',
            'POST',
            $payload
        );

        $command = $this->reflectMethod($this->baseController, 'getCommand', [CommandDouble::class, $request, $metadata]);

        static::assertInstanceOf(CommandDouble::class, $command);
        static::assertSame('20m', $command->getRoomSize());
        $this->expectException(CommandPayloadKeyMissing::class);
        $command->getInvalidPayloadParam();
    }

    public function testGetCommandWithInvalidMetadataKey(): void
    {
        $metadata = ['requester' => 'boilerplate'];
        $payload = ['room_size' => '20m'];
        $request = Request::create(
            '/v1/buyer/1/update-buyer-search-profile',
            'POST',
            $payload
        );

        $command = $this->reflectMethod($this->baseController, 'getCommand', [CommandDouble::class, $request, $metadata]);

        static::assertInstanceOf(CommandDouble::class, $command);
        static::assertSame('20m', $command->getRoomSize());
        $this->expectException(CommandMetadataKeyMissing::class);
        $command->getMeta('invalid_meta_key');
    }

    public function testGetCommandHandler(): void
    {
        $payload = ['room_size' => '20m'];

        $command = new CommandDouble($payload);
        $commandHandler = $this->reflectMethod($this->baseController, 'getCommandHandler', [CommandHandlerDouble::class]);
        $result = $commandHandler->handleCommandDouble($command);

        static::assertInstanceOf(CommandHandler::class, $commandHandler);
        static::assertInstanceOf(CommandHandlerDouble::class, $commandHandler);
        static::assertSame($result, $command->getRoomSize());
    }

    /**
     * @param array<mixed> $args
     *
     * @throws ReflectionException
     */
    private function reflectMethod(object $obj, string $methodName, array $args = []): mixed
    {
        $reflectedClass = new ReflectionClass($obj);
        $reflectedMethod = $reflectedClass->getMethod($methodName);
        $reflectedMethod->setAccessible(true);

        return $reflectedMethod->invokeArgs($obj, $args);
    }
}
