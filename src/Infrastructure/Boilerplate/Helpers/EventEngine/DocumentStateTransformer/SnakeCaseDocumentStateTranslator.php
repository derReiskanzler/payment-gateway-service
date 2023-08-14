<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer;

use Illuminate\Support\Str;

final class SnakeCaseDocumentStateTranslator implements DocumentStateTranslatorInterface
{
    /**
     * @param array<string, mixed> $state
     *
     * @return array<string, mixed>
     */
    public function getToStoreState(array $state): array
    {
        $transformedPayload = [];
        foreach ($state as $key => $value) {
            $key = Str::snake($key);
            $transformedPayload[$key] = \is_array($value)
                ? $this->getToStoreState($value)
                : $value;
        }

        return $transformedPayload;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function getToCreateState(array $data): array
    {
        $transformedPayload = [];
        foreach ($data as $key => $value) {
            $key = Str::camel($key);
            $transformedPayload[$key] = \is_array($value)
                ? $this->getToCreateState($value)
                : $value;
        }

        return $transformedPayload;
    }
}
