<?php

declare(strict_types=1);

use Allmyhomes\Infrastructure\Boilerplate\Helpers\EventStore\Migrations\EventStreamMigration;
use EventEngine\Persistence\MultiModelStore;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Schema;

final class CreateDepositPaymentEmailStream extends EventStreamMigration
{
    private const STREAM_NAME = 'payment_gateway-deposit_payment_email-stream';

    protected bool $migrateOnEventStore = true;

    /**
     * Run the migrations.
     *
     * @throws BindingResolutionException
     */
    public function up(): void
    {
        $store = app()->make(MultiModelStore::class);

        $store->createStream(self::STREAM_NAME);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::STREAM_NAME);
    }
}
