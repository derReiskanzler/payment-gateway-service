<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer;

use EventEngine\Messaging\Message;
use Illuminate\Support\Str;

class SnakeCaseEventPayloadTranslator implements EventPayloadTranslatorInterface
{
    /**
     * @return array<string, string>
     */
    public function getPayloadToGenericEvent(Message $message): array
    {
        return $this->translatePayloadToSnakeCase($message->payload());
    }

    /**
     * @return array<string, string>
     */
    public function getPayloadToDomainEvent(Message $message): array
    {
        return $this->translatePayloadFromSnakeCase($message->payload());
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function translatePayloadToSnakeCase(array $payload): array
    {
        $transformedPayload = [];
        foreach ($payload as $key => $value) {
            $key = Str::snake($key);
            $transformedPayload[$key] = \is_array($value)
                ? $this->translatePayloadToSnakeCase($value)
                : $value;
        }

        return $transformedPayload;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function translatePayloadFromSnakeCase(array $payload): array
    {
        $transformedPayload = [];
        foreach ($payload as $key => $value) {
            $key = Str::camel($key);
            $transformedPayload[$key] = \is_array($value)
                ? $this->translatePayloadFromSnakeCase($value)
                : $value;
        }

        return $transformedPayload;
    }
}
