<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Exceptions\CommandMetadataKeyMissing;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Exceptions\CommandPayloadKeyMissing;
use Exception;

/**
 * @deprecated Please implement the interface Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\Command directly in the Command Class
 */
abstract class AbstractCommand implements Command
{
    private CommandId $commandId;

    /**
     * AbstractCommand constructor.
     *
     * @param array<string, mixed> $payload   Payload
     * @param array<string, mixed> $metadata  Metadata
     * @param CommandId|null       $commandId Command Id
     *
     * @throws Exception
     */
    public function __construct(
        private array $payload,
        private array $metadata = [],
        ?CommandId $commandId = null
    ) {
        $this->commandId = $commandId ?? CommandId::generate();
    }

    abstract public function commandName(): CommandName;

    final public function uuid(): CommandId
    {
        return $this->commandId;
    }

    final public function metadata(): array
    {
        return $this->metadata;
    }

    /**
     * Method to access specific metadata.
     *
     * @param string $key Metadata Key
     */
    final public function getMeta(string $key): mixed
    {
        if (!\array_key_exists($key, $this->metadata)) {
            throw CommandMetadataKeyMissing::withKeyOfCommand($key, $this->commandName());
        }

        return $this->metadata[$key];
    }

    /**
     * Method to access specific metadata.
     *
     * In case key is not present, a default is returned
     *
     * @param string $key     Metadata Key
     * @param mixed  $default Default Value
     */
    final public function getMetaOrDefault(string $key, mixed $default): mixed
    {
        if (!\array_key_exists($key, $this->metadata)) {
            return $default;
        }

        return $this->metadata[$key];
    }

    /**
     * Internal method to access raw payload values.
     *
     * @param string $key Payload Key
     *
     * @throws CommandPayloadKeyMissing
     */
    protected function get(string $key): mixed
    {
        if (!\array_key_exists($key, $this->payload)) {
            throw CommandPayloadKeyMissing::withKeyOfCommand($key, $this->commandName());
        }

        return $this->payload[$key];
    }

    /**
     * Internal method to access raw payload values.
     *
     * In case key is not present, a default is returned
     *
     * @param string $key     Payload Key
     * @param mixed  $default Default Value
     */
    protected function getOrDefault(string $key, mixed $default): mixed
    {
        if (!\array_key_exists($key, $this->payload)) {
            return $default;
        }

        return $this->payload[$key];
    }
}
