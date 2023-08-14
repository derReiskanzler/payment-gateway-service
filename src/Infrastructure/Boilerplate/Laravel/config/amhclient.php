<?php

declare(strict_types=1);

return [
    'amh_oauth_credentials' => [
        'client_id' => env('APP_AMH_OAUTH_CLIENT_ID'),
        'client_secret' => env('APP_AMH_OAUTH_CLIENT_SECRET'),
        'grant_type' => env('APP_AMH_OAUTH_GRANT_TYPE', 'client_credentials'),
        'username' => env('APP_AMH_OAUTH_USERNAME', ''),
        'password' => env('APP_AMH_OAUTH_PASSWORD', ''),
    ],
    'service' => [
        'auth' => [
            'base_url' => env('AUTH_BASE_URL', 'http://auth'),
        ],
        'brokerage-management' => [
            'base_url' => env('BROKERAGE_MANAGEMENT_BASE_URL', 'http://brokerage-management'),
        ],
        'buyer' => [
            'base_url' => env('BUYER_BASE_URL', 'http://buyer'),
        ],
        'lead' => [
            'base_url' => env('LEAD_BASE_URL', 'http://lead'),
        ],
        'mail' => [
            'base_url' => env('MAIL_BASE_URL', 'http://mail'),
        ],
        'project-information' => [
            'base_url' => env('PROJECT_INFORMATION_BASE_URL', 'http://project-information'),
        ],
        'project-setting' => [
            'base_url' => env('PROJECT_SETTING_BASE_URL', 'http://project-setting'),
        ],
        'be-qualification' => [
            'base_url' => env('BE_QUALIFICATION_BASE_URL', 'http://be-qualification'),
        ],
        'realtime' => [
            'base_url' => env('REALTIME_BASE_URL', 'http://realtime'),
        ],
        'reservation' => [
            'base_url' => env('RESERVATION_BASE_URL', 'http://reservation'),
        ],
        'user' => [
            'base_url' => env('USER_BASE_URL', 'http://user'),
        ],
    ],
];
