<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\ReadModel;

interface ReadModelRepositoryInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function getDocument(string $id): ?array;

    /**
     * @param array<string, mixed> $docPayload
     */
    public function addDocument(string $id, array $docPayload): void;

    /**
     * @param array<string, mixed> $docPayload
     */
    public function upsertDocument(string $id, array $docPayload): void;

    /**
     * @param array<string, mixed> $docPayloadOrSubset
     */
    public function updateDocument(string $id, array $docPayloadOrSubset): void;

    public function deleteDocument(string $id): void;
}
