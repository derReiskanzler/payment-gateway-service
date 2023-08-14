<?php

/** @noinspection PhpUndefinedVariableInspection */

declare(strict_types=1);

use Allmyhomes\Contract\Mock\Helper\MockHelper;
use Dredd\Hooks;

/*
 * uncomment if using mock server
Hooks::beforeAll(function (&$transactions) {
    MockHelper::startMockServer();
});
Hooks::afterAll(function (&$transactions) {
    MockHelper::stopMockServer();
});
*/

Hooks::beforeEach(static function (&$transaction) use ($faker): void {
    $faker->setScopes(['scope:name']);
    if ('403' === $transaction->expected->statusCode) {
        $faker->setScopes(['nothing:correct']);
    }
    if ('401' !== $transaction->expected->statusCode) {
        $transaction->request->headers->Authorization = 'Bearer '.$faker->getToken();
    }
});

// include api specific hook-files
