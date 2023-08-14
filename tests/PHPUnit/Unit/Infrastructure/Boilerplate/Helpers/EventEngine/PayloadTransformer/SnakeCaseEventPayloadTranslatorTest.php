<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventEngine\PayloadTransformer\SnakeCaseEventPayloadTranslator;
use EventEngine\Messaging\Message;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class SnakeCaseEventPayloadTranslatorTest extends TestCase
{
    public function testGetPayloadToGenericEvent(): void
    {
        /** @var Message&MockInterface $mockedMessage */
        $mockedMessage = $this->generateDomainPayload();
        $payloadTranslator = new SnakeCaseEventPayloadTranslator();

        $genericPayload = $payloadTranslator->getPayloadToGenericEvent($mockedMessage);

        static::assertSame([
            'unit_id' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'unit_name' => 'Test',
        ], $genericPayload);
    }

    public function testGetPayloadToGenericEventDeepNesting(): void
    {
        /** @var Message&MockInterface $mockedMessage */
        $mockedMessage = $this->generateDomainPayloadDeepNested();
        $payloadTranslator = new SnakeCaseEventPayloadTranslator();

        $genericPayload = $payloadTranslator->getPayloadToGenericEvent($mockedMessage);

        static::assertSame([
            'unit_id' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'prospect_contact' => [
                'first_name' => 'John',
                'last_name' => 'Smith',
            ],
        ], $genericPayload);
    }

    public function testGetPayloadToDomainEvent(): void
    {
        /** @var Message&MockInterface $mockedMessage */
        $mockedMessage = $this->generateGenericPayload();
        $payloadTranslator = new SnakeCaseEventPayloadTranslator();

        $domainPayload = $payloadTranslator->getPayloadToDomainEvent($mockedMessage);

        static::assertSame([
            'unitId' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'unitName' => 'Test',
        ], $domainPayload);
    }

    public function testGetPayloadToDomainEventDeepNesting(): void
    {
        /** @var Message&MockInterface $mockedMessage */
        $mockedMessage = $this->generateGenericPayloadDeepNesting();
        $payloadTranslator = new SnakeCaseEventPayloadTranslator();

        $domainPayload = $payloadTranslator->getPayloadToDomainEvent($mockedMessage);

        static::assertSame([
            'unitId' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
            'prospectContact' => [
                'firstName' => 'John',
                'lastName' => 'Smith',
            ],
        ], $domainPayload);
    }

    private function generateDomainPayload(): MockInterface
    {
        return Mockery::mock(Message::class)
            ->makePartial()
            ->allows('payload')
            ->andReturns([
                'unitId' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
                'unitName' => 'Test',
            ])
            ->getMock();
    }

    private function generateDomainPayloadDeepNested(): MockInterface
    {
        return Mockery::mock(Message::class)
            ->makePartial()
            ->allows('payload')
            ->andReturns([
                'unitId' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
                'prospectContact' => [
                    'firstName' => 'John',
                    'lastName' => 'Smith',
                ],
            ])
            ->getMock();
    }

    private function generateGenericPayload(): MockInterface
    {
        return Mockery::mock(Message::class)
            ->makePartial()
            ->allows('payload')
            ->andReturns([
                'unit_id' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
                'unit_name' => 'Test',
            ])
            ->getMock();
    }

    private function generateGenericPayloadDeepNesting(): MockInterface
    {
        return Mockery::mock(Message::class)
            ->makePartial()
            ->allows('payload')
            ->andReturns([
                'unit_id' => '25cff157-d023-4b81-bfca-a02bb3aa5c35',
                'prospect_contact' => [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                ],
            ])
            ->getMock();
    }
}
