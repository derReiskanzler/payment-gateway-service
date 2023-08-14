<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

$api = app(Route::class);

$api::group([], static function ($api): void {
    // Smoke test
    $api->get('/healthz', 'Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Controllers\HealthzController@healthz')->name('healthz');
});
