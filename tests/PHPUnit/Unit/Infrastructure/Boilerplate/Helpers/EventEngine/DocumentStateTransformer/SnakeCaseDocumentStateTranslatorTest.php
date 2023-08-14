<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\DocumentStateTransformer\SnakeCaseDocumentStateTranslator;
use PHPUnit\Framework\TestCase;

final class SnakeCaseDocumentStateTranslatorTest extends TestCase
{
    public function testGetToStoreState(): void
    {
        $immutableRecordPayload = $this->generateStatePayload();
        $documentStateTranslator = new SnakeCaseDocumentStateTranslator();

        $storageState = $documentStateTranslator->getToStoreState($immutableRecordPayload);

        self::assertSame([
            'unit_id' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'unit_name' => 'Test',
        ], $storageState);
    }

    public function testGetToStoreStateDeepNesting(): void
    {
        $immutableRecordPayload = $this->generateStatePayloadDeepNested();
        $documentStateTranslator = new SnakeCaseDocumentStateTranslator();

        $storageState = $documentStateTranslator->getToStoreState($immutableRecordPayload);

        self::assertSame([
            'unit_id' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'prospect_contact' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
            ],
        ], $storageState);
    }

    public function testGetToCreateState(): void
    {
        $storageState = $this->generateGenericPayload();
        $documentStateTranslator = new SnakeCaseDocumentStateTranslator();

        $immutableRecordPayload = $documentStateTranslator->getToCreateState($storageState);

        self::assertSame([
            'unitId' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'unitName' => 'Test',
        ], $immutableRecordPayload);
    }

    public function testGetToCreateStateDeepNesting(): void
    {
        $storageState = $this->generateGenericPayloadDeepNesting();
        $documentStateTranslator = new SnakeCaseDocumentStateTranslator();

        $immutableRecordPayload = $documentStateTranslator->getToCreateState($storageState);

        self::assertSame([
            'unitId' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'prospectContact' => [
                'firstName' => 'John',
                'lastName' => 'Smith',
            ],
        ], $immutableRecordPayload);
    }

    /**
     * @return string[]
     */
    private function generateStatePayload(): array
    {
        return [
            'unitId' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'unitName' => 'Test',
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function generateStatePayloadDeepNested(): array
    {
        return [
            'unitId' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'prospectContact' => [
                'firstName' => 'John',
                'lastName' => 'Smith',
            ],
        ];
    }

    /**
     * @return string[]
     */
    private function generateGenericPayload(): array
    {
        return [
            'unit_id' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'unit_name' => 'Test',
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function generateGenericPayloadDeepNesting(): array
    {
        return [
            'unit_id' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'prospect_contact' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
            ],
        ];
    }
}
