<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Laravel\App\Listeners;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Events\ProophEventStoredInStream;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Listeners\LaravelProduceProjectorProophEventStoredListener;
use DateTimeImmutable;
use EventEngine\Messaging\GenericEvent;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LaravelProduceProjectorProophEventStoredListenerTest extends TestCase
{
    public function testLaravelProduceProjectorProophEventStoredListenerIsDispatched(): void
    {
        Event::fake();

        /** @var GenericEvent $event */
        $event = GenericEvent::fromArray([
            'message_name' => 'Test.TestEvent',
            'uuid' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
            'payload' => [
                'user_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42577',
                'name' => 'Jane Doe',
            ],
            'metadata' => [],
            'created_at' => DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', '2020-06-06T10:00:05'),
        ]);

        event(new ProophEventStoredInStream($event, 'test-table-stream'));

        Event::assertDispatched(ProophEventStoredInStream::class);
    }

    public function testLaravelProduceProjectorProophEventStoredListenerIsCalled(): void
    {
        Queue::fake();

        /** @var GenericEvent $event */
        $event = GenericEvent::fromArray([
            'message_name' => 'Test.TestEvent',
            'uuid' => 'dc243dd9-cfae-4cfd-83df-0ca016a42566',
            'payload' => [
                'user_id' => 'dc243dd9-cfae-4cfd-83df-0ca016a42577',
                'name' => 'Jane Doe',
            ],
            'metadata' => [],
            'created_at' => DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', '2020-06-06T10:00:05'),
        ]);

        event(new ProophEventStoredInStream($event, 'test-table-stream'));

        Queue::assertPushed(CallQueuedListener::class, static fn ($job) => LaravelProduceProjectorProophEventStoredListener::class === $job->class);
    }
}
