<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

$api = app(Route::class);

$api::group([], static function ($api): void {
    require __DIR__.'/v1/complete-deposit-payment-session.php';
});
