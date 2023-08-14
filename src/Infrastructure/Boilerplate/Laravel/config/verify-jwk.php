<?php

declare(strict_types=1);

/**
 * TokenProviders for usage with the middleware Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Middleware\VerifyJWK\VerifyJWK.
 *
 * Each tokenProvider that communicates with you shall have an entry with the following sub-keys:
 * - jwkPath -> path to the jwk should be a local file but can be a remote file as well.
 * - audiences -> array of audiences your service belongs to. Requests that are not intended for at least one of the configured audiences are rejected
 * - issuers -> array of issues that shall be listened to. Requests from other issues are rejected.
 * - algorithms -> array of accepted algorithms
 * - additionalClaims - any additional claims that MUST appear 1-1 in the token
 */
return [
    'google' => [
        'jwkPath' => 'https://www.googleapis.com/oauth2/v3/certs',
        'audiences' => [
            // replace these entries with your actual audience value in subscription tf configuration; and remove this comment ;)
            'https://api.'.env('BASE_DOMAIN', 'develop.envs.io').'/prototype-cms/v1/broker/reserve-unit',
            'https://api.'.env('BASE_DOMAIN', 'develop.envs.io').'/prototype-cms/v1/broker/unreserve-unit',
        ],
        'issuers' => [
            'https://accounts.google.com',
            'accounts.google.com',
        ],
        'algorithms' => [
            'RS256',
        ],
        'additionalClaims' => [
            'email_verified' => true,
            'email' => env('PUBSUB_SERVICE_ACCOUNT_DEFAULT', 'pubsub@amh-develop-217408.iam.gserviceaccount.com'),
        ],
    ],
];
