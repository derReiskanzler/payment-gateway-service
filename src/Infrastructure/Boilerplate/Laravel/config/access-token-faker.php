<?php

declare(strict_types=1);

return [
    /*
     * Default claims that are set at initialization and by calling the "resetClaims()"-method on the Services
     */
    'predefined_claims' => [
        'aud' => env('APP_AMH_OAUTH_CLIENT_ID', 999),
        'sub' => '', // needed by our default algorithm
    ],
];
