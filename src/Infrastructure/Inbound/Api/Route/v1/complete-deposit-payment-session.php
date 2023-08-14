<?php

declare(strict_types=1);

use Allmyhomes\Infrastructure\Inbound\Api\CompleteDepositPaymentSession\v1\Controller\CompleteDepositPaymentSessionController;
use Allmyhomes\Infrastructure\Inbound\Api\Route\Routes;
use Illuminate\Routing\Router;

/* @var Router $api */
$api->group(
    [
        'prefix' => Routes::VERSION_1,
        'middleware' => [
            'verify-webhook-signature',
        ],
    ],
    function ($api) {
        $api->post(
            Routes::COMPLETE_DEPOSIT_PAYMENT_SESSION,
            CompleteDepositPaymentSessionController::class,
        );
    }
);
