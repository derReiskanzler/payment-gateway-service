<?php

declare(strict_types=1);

use Allmyhomes\Application\UseCase\CreateDepositPaymentSession\ProcessManager\ReservationAcceptedByInventoryProcessManager;
use Allmyhomes\Application\UseCase\PopulateProspect\Projector\ProspectsProjector;
use Allmyhomes\Application\UseCase\PopulateReservation\Projector\ReservationsProjector;
use Allmyhomes\Application\UseCase\PopulateUnit\Projector\UnitsProjector;
use Allmyhomes\Application\UseCase\RetryDepositPaymentSessionCreation\ProcessManager\DepositPaymentSessionCreationFailedProcessManager;
use Allmyhomes\Application\UseCase\SendDepositPaymentEmailToProspect\ProcessManager\DepositPaymentSessionCreatedProcessManager;
use Allmyhomes\EventProjections\Services\Configurations\Environment;
use Allmyhomes\Infrastructure\Projection;
use Allmyhomes\Infrastructure\Stream;

return [
    /*
    |--------------------------------------------------------------------------
    | Projections
    |--------------------------------------------------------------------------
    |
    | Every projection configuration must consist of a stream-name.
    | Additionally, consuming projection configurations need an environment and
    | the class name of a eventHandler.
    | Supported environments are 'local' and 'shared'
    |
    |
    */

    'producing' => [
        Projection::PRODUCE_DEPOSIT_PAYMENT_SESSION_PROJECTION => [
            'stream_name' => Stream::PAYMENT_GATEWAY_DEPOSIT_PAYMENT_SESSION_STREAM,
        ],
        Projection::PRODUCE_DEPOSIT_PAYMENT_EMAIL_PROJECTION => [
            'stream_name' => Stream::PAYMENT_GATEWAY_DEPOSIT_PAYMENT_EMAIL_STREAM,
        ],
    ],

    'consuming' => [
        Projection::UNITS_PROJECTION => [
            'stream_name' => Stream::PROJECT_INFORMATION_SELLABLE_PROJECT_UNIT_CONTENTS_STREAM,
            'environment' => Environment::SHARED,
            'handler' => UnitsProjector::class,
        ],
        Projection::RESERVATIONS_PROJECTION => [
            'stream_name' => Stream::RESERVATION_MANAGEMENT_RESERVATION_STREAM,
            'environment' => Environment::SHARED,
            'handler' => ReservationsProjector::class,
        ],
        Projection::PROSPECTS_PROJECTION => [
            'stream_names' => [
                Stream::USER_USERS_STREAM,
                Stream::KEYCLOAK_ADAPTER_PROSPECTS_STREAM,
            ],
            'environment' => Environment::SHARED,
            'handler' => ProspectsProjector::class,
        ],
        Projection::CREATE_DEPOSIT_PAYMENT_SESSION_PROJECTION => [
            'stream_name' => Stream::RESERVATION_MANAGEMENT_RESERVATION_STREAM,
            'environment' => Environment::SHARED,
            'handler' => ReservationAcceptedByInventoryProcessManager::class,
        ],
        Projection::RETRY_DEPOSIT_PAYMENT_SESSION_PROJECTION => [
            'stream_name' => Stream::PAYMENT_GATEWAY_DEPOSIT_PAYMENT_SESSION_STREAM,
            'environment' => Environment::LOCAL,
            'handler' => DepositPaymentSessionCreationFailedProcessManager::class,
        ],
        Projection::SEND_DEPOSIT_PAYMENT_EMAIL_TO_PROSPECT_PROJECTION => [
            'stream_name' => Stream::PAYMENT_GATEWAY_DEPOSIT_PAYMENT_SESSION_STREAM,
            'environment' => Environment::LOCAL,
            'handler' => DepositPaymentSessionCreatedProcessManager::class,
        ],
    ],
];
