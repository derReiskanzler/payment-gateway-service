<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Helpers\EventEngine;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer\SnakeCaseDocumentStateTranslator;
use EventEngine\DocumentStore\Filter\AnyOfDocIdFilter;
use EventEngine\DocumentStore\Filter\DocIdFilter;
use EventEngine\DocumentStore\Filter\EqFilter;
use EventEngine\Persistence\InMemoryConnection;
use EventEngine\Persistence\MultiModelStore;
use EventEngine\Prooph\V7\EventStore\InMemoryMultiModelStore;
use Tests\doubles\DoubleDocumentStoreReadModelRepository;
use Tests\TestCase;
use Throwable;

final class DocumentStoreReadModelRepositoryTest extends TestCase
{
    private MultiModelStore $store;
    private DoubleDocumentStoreReadModelRepository $doubleDocumentStoreReadModelRepository;

    protected function setUp(): void
    {
        $this->store = InMemoryMultiModelStore::fromConnection(new InMemoryConnection());
        $this->doubleDocumentStoreReadModelRepository = new DoubleDocumentStoreReadModelRepository(
            $this->store,
            new SnakeCaseDocumentStateTranslator()
        );

        $this->store->addCollection($this->doubleDocumentStoreReadModelRepository->collectionName());
    }

    /**
     * @throws Throwable
     */
    public function testGetDocument(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['name' => 'Test'];
        $this->addDocument($documentId, $documentPayload);

        $document = $this->doubleDocumentStoreReadModelRepository->getDocument($documentId);

        self::assertSame(['name' => 'Test'], $document);
    }

    public function testGetDocumentThatDoesntExist(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';

        $document = $this->doubleDocumentStoreReadModelRepository->getDocument($documentId);

        self::assertNull($document);
    }

    public function testFindDocuments(): void
    {
        $documentIdOne = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayloadOne = ['name' => 'Test'];
        $this->addDocument($documentIdOne, $documentPayloadOne);

        $documentIdTwo = '083ecaa1-7fe6-47fa-8d24-3ffe509900ae';
        $documentPayloadTwo = ['name' => 'Test'];
        $this->addDocument($documentIdTwo, $documentPayloadTwo);

        $documents = $this->doubleDocumentStoreReadModelRepository->findDocuments(new EqFilter('name', 'Test'));

        self::assertSame(2, iterator_count($documents));
    }

    public function testFindDocumentsWithLimit(): void
    {
        $documentIdOne = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayloadOne = ['name' => 'Test'];
        $this->addDocument($documentIdOne, $documentPayloadOne);

        $documentIdTwo = '083ecaa1-7fe6-47fa-8d24-3ffe509900ae';
        $documentPayloadTwo = ['name' => 'Test'];
        $this->addDocument($documentIdTwo, $documentPayloadTwo);

        $documents = $this->doubleDocumentStoreReadModelRepository->findDocuments(new EqFilter('name', 'Test'), null, 1);

        self::assertSame(1, iterator_count($documents));
        self::assertSame($documentIdOne, key(iterator_to_array($documents)));
    }

    public function testFindDocumentsWithSkip(): void
    {
        $documentIdOne = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayloadOne = ['name' => 'Test'];
        $this->addDocument($documentIdOne, $documentPayloadOne);

        $documentIdTwo = '083ecaa1-7fe6-47fa-8d24-3ffe509900ae';
        $documentPayloadTwo = ['name' => 'Test'];
        $this->addDocument($documentIdTwo, $documentPayloadTwo);

        $documents = $this->doubleDocumentStoreReadModelRepository->findDocuments(new EqFilter('name', 'Test'), 1);

        self::assertSame(1, iterator_count($documents));
        self::assertSame($documentIdTwo, key(iterator_to_array($documents)));
    }

    /**
     * @throws Throwable
     */
    public function testHasDocument(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['name' => 'Test'];
        $this->addDocument($documentId, $documentPayload);

        $hasDocument = $this->doubleDocumentStoreReadModelRepository->hasDocument(new DocIdFilter($documentId));

        self::assertTrue($hasDocument);
    }

    public function testAddDocument(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['name' => 'Test'];

        $this->doubleDocumentStoreReadModelRepository->addDocument($documentId, $documentPayload);

        self::assertSame(['name' => 'Test'], $this->getDocument($documentId));
    }

    public function testAddDocumentWithCamelCaseKey(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['firstName' => 'Test'];

        $this->doubleDocumentStoreReadModelRepository->addDocument($documentId, $documentPayload);

        self::assertSame(['first_name' => 'Test'], $this->getDocument($documentId));
    }

    public function testAddDocumentWithSnakeCaseKey(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['first_name' => 'Test'];

        $this->doubleDocumentStoreReadModelRepository->addDocument($documentId, $documentPayload);

        self::assertSame(['first_name' => 'Test'], $this->getDocument($documentId));
    }

    public function testUpsertDocumentAsInsert(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['name' => 'Test'];

        $this->doubleDocumentStoreReadModelRepository->upsertDocument($documentId, $documentPayload);

        self::assertSame(['name' => 'Test'], $this->getDocument($documentId));
    }

    /**
     * @throws Throwable
     */
    public function testUpsertDocumentAsUpdate(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['name' => 'Test'];
        $this->addDocument($documentId, $documentPayload);

        $updatedDocumentPayload = ['name' => 'Test-1', 'description' => 'full description'];
        $this->doubleDocumentStoreReadModelRepository->upsertDocument($documentId, $updatedDocumentPayload);

        self::assertSame(['name' => 'Test-1', 'description' => 'full description'], $this->getDocument($documentId));
    }

    public function testUpsertDocumentWithCamelCase(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['firstName' => 'Test'];

        $this->doubleDocumentStoreReadModelRepository->upsertDocument($documentId, $documentPayload);

        self::assertSame(['first_name' => 'Test'], $this->getDocument($documentId));
    }

    public function testUpsertDocumentWithSnakeCase(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['first_name' => 'Test'];

        $this->doubleDocumentStoreReadModelRepository->upsertDocument($documentId, $documentPayload);

        self::assertSame(['first_name' => 'Test'], $this->getDocument($documentId));
    }

    /**
     * @throws Throwable
     */
    public function testUpdateFullDocument(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['name' => 'Test'];
        $this->addDocument($documentId, $documentPayload);

        $updatedDocumentPayload = ['name' => 'Test-1', 'description' => 'full description'];
        $this->doubleDocumentStoreReadModelRepository->updateDocument($documentId, $updatedDocumentPayload);

        self::assertSame(['name' => 'Test-1', 'description' => 'full description'], $this->getDocument($documentId));
    }

    /**
     * @throws Throwable
     */
    public function testUpdatePartialDocument(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['name' => 'Test'];
        $this->addDocument($documentId, $documentPayload);

        $updatedDocumentPayload = ['description' => 'full description'];
        $this->doubleDocumentStoreReadModelRepository->updateDocument($documentId, $updatedDocumentPayload);

        self::assertSame(['name' => 'Test', 'description' => 'full description'], $this->getDocument($documentId));
    }

    /**
     * @throws Throwable
     */
    public function testUpdateDocumentWithCamelCaseKey(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['name' => 'Test'];
        $this->addDocument($documentId, $documentPayload);

        $updatedDocumentPayload = ['name' => 'Test-1', 'fullDescription' => 'full description'];
        $this->doubleDocumentStoreReadModelRepository->updateDocument($documentId, $updatedDocumentPayload);

        self::assertSame(['name' => 'Test-1', 'full_description' => 'full description'], $this->getDocument($documentId));
    }

    /**
     * @throws Throwable
     */
    public function testUpdateDocumentWithSnakeCaseKey(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['name' => 'Test'];
        $this->addDocument($documentId, $documentPayload);

        $updatedDocumentPayload = ['name' => 'Test-1', 'full_description' => 'full description'];
        $this->doubleDocumentStoreReadModelRepository->updateDocument($documentId, $updatedDocumentPayload);

        self::assertSame(['name' => 'Test-1', 'full_description' => 'full description'], $this->getDocument($documentId));
    }

    /**
     * @throws Throwable
     */
    public function testUpdateManyDocumentsWithCamelCaseKey(): void
    {
        $documentIdOne = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayloadOne = ['name' => 'Test'];
        $this->addDocument($documentIdOne, $documentPayloadOne);

        $documentIdTwo = '083ecaa1-7fe6-47fa-8d24-3ffe509900ae';
        $documentPayloadTwo = ['name' => 'Test'];
        $this->addDocument($documentIdTwo, $documentPayloadTwo);

        $updatedDocumentPayload = ['fullDescription' => 'full description'];
        $this->doubleDocumentStoreReadModelRepository->updateMany(
            new AnyOfDocIdFilter([$documentIdOne, $documentIdTwo]),
            $updatedDocumentPayload
        );

        self::assertSame(['name' => 'Test', 'full_description' => 'full description'], $this->getDocument($documentIdOne));
        self::assertSame(['name' => 'Test', 'full_description' => 'full description'], $this->getDocument($documentIdTwo));
    }

    /**
     * @throws Throwable
     */
    public function testUpdateManyDocumentsWithSnakeCaseKey(): void
    {
        $documentIdOne = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayloadOne = ['name' => 'Test'];
        $this->addDocument($documentIdOne, $documentPayloadOne);

        $documentIdTwo = '083ecaa1-7fe6-47fa-8d24-3ffe509900ae';
        $documentPayloadTwo = ['name' => 'Test'];
        $this->addDocument($documentIdTwo, $documentPayloadTwo);

        $updatedDocumentPayload = ['full_description' => 'full description'];
        $this->doubleDocumentStoreReadModelRepository->updateMany(
            new AnyOfDocIdFilter([$documentIdOne, $documentIdTwo]),
            $updatedDocumentPayload
        );

        self::assertSame(['name' => 'Test', 'full_description' => 'full description'], $this->getDocument($documentIdOne));
        self::assertSame(['name' => 'Test', 'full_description' => 'full description'], $this->getDocument($documentIdTwo));
    }

    /**
     * @throws Throwable
     */
    public function testDeleteDocument(): void
    {
        $documentId = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayload = ['name' => 'Test'];
        $this->addDocument($documentId, $documentPayload);

        $this->doubleDocumentStoreReadModelRepository->deleteDocument($documentId);

        self::assertNull($this->getDocument($documentId));
    }

    /**
     * @throws Throwable
     */
    public function testDeleteManyDocuments(): void
    {
        $documentIdOne = '083ecaa1-7fe6-47fa-8d24-3ffe509900ad';
        $documentPayloadOne = ['name' => 'Test'];
        $this->addDocument($documentIdOne, $documentPayloadOne);

        $documentIdTwo = '083ecaa1-7fe6-47fa-8d24-3ffe509900ae';
        $documentPayloadTwo = ['name' => 'Test'];
        $this->addDocument($documentIdTwo, $documentPayloadTwo);

        $this->doubleDocumentStoreReadModelRepository->deleteMany(new AnyOfDocIdFilter([$documentIdOne, $documentIdTwo]));

        self::assertNull($this->getDocument($documentIdOne));
        self::assertNull($this->getDocument($documentIdTwo));
    }

    /**
     * @param array<mixed> $documentPayload
     *
     * @throws Throwable
     */
    private function addDocument(string $documentId, array $documentPayload): void
    {
        $this->store->addDoc($this->doubleDocumentStoreReadModelRepository->collectionName(), $documentId, $documentPayload);
    }

    /**
     * @return array<mixed>|null
     */
    private function getDocument(string $documentId): ?array
    {
        return $this->store->getDoc($this->doubleDocumentStoreReadModelRepository->collectionName(), $documentId);
    }
}
