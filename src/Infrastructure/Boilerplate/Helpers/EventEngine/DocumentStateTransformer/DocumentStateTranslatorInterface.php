<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer;

interface DocumentStateTranslatorInterface
{
    /**
     * Converts the data to store the state object e.g. camelCase to snake_case.
     *
     * @param array<string, mixed> $state
     *
     * @return array<string, mixed>
     */
    public function getToStoreState(array $state): array;

    /**
     * Converts the data to create the state object  e.g. snake_case to camelCase.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function getToCreateState(array $data): array;
}
