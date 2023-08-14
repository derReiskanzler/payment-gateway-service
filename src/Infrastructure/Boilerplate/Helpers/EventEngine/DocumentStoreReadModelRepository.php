<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Cqrs\ReadModel\ReadModelRepositoryInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer\DocumentStateTranslatorInterface;
use EventEngine\DocumentStore\DocumentStore;
use EventEngine\DocumentStore\Filter\Filter;
use Traversable;

abstract class DocumentStoreReadModelRepository implements ReadModelRepositoryInterface
{
    public function __construct(
        private DocumentStore $documentStore,
        private DocumentStateTranslatorInterface $documentStateTranslator
    ) {
    }

    abstract public function collectionName(): string;

    /**
     * @return array<string, mixed>|null
     */
    final public function getDocument(string $id): ?array
    {
        return $this->documentStore->getDoc(
            $this->collectionName(),
            $id,
        );
    }

    final public function hasDocument(Filter $filter): bool
    {
        $count = $this->documentStore->countDocs(
            $this->collectionName(),
            $filter
        );

        return 1 === $count;
    }

    /**
     * @return Traversable<string|int, mixed>
     */
    final public function findDocuments(
        Filter $filter,
        ?int $skip = null,
        ?int $limit = null
    ): Traversable {
        return $this->documentStore->findDocs(
            $this->collectionName(),
            $filter,
            $skip,
            $limit,
        );
    }

    /**
     * @param array<string, mixed> $docPayload
     */
    final public function addDocument(string $id, array $docPayload): void
    {
        $this->documentStore->addDoc(
            $this->collectionName(),
            $id,
            $this->documentStateTranslator->getToStoreState($docPayload)
        );
    }

    /**
     * @param array<string, mixed> $docPayload
     */
    final public function upsertDocument(string $id, array $docPayload): void
    {
        $this->documentStore->upsertDoc(
            $this->collectionName(),
            $id,
            $this->documentStateTranslator->getToStoreState($docPayload)
        );
    }

    /**
     * @param array<string, mixed> $docPayloadOrSubset
     */
    final public function updateDocument(string $id, array $docPayloadOrSubset): void
    {
        $this->documentStore->updateDoc(
            $this->collectionName(),
            $id,
            $this->documentStateTranslator->getToStoreState($docPayloadOrSubset)
        );
    }

    /**
     * @param array<string, mixed> $dataSet
     */
    final public function updateMany(Filter $filter, array $dataSet): void
    {
        $this->documentStore->updateMany(
            $this->collectionName(),
            $filter,
            set: $this->documentStateTranslator->getToStoreState($dataSet)
        );
    }

    final public function deleteDocument(string $id): void
    {
        $this->documentStore->deleteDoc(
            $this->collectionName(),
            $id
        );
    }

    final public function deleteMany(Filter $filter): void
    {
        $this->documentStore->deleteMany(
            $this->collectionName(),
            $filter
        );
    }
}
