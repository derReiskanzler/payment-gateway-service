<?php

declare(strict_types=1);

use Illuminate\Log\LogManager;

function logOnShutdown(): void
{
    $errors = error_get_last();
    if (!$errors) {
        return;
    }

    try { // Log attempt only if Class config exists
        /** @var LogManager $logger */
        $logger = app()->get('log');
        $logger->critical('Unexpected application shutdown!', ['errors' => $errors]);
    } catch (Throwable) {
        return;
    }
}
