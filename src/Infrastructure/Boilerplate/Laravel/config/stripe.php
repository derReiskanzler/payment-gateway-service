<?php

declare(strict_types=1);

use Allmyhomes\Infrastructure\Config\DepositPaymentSessionConfig;

return [
    DepositPaymentSessionConfig::API_KEY => env('STRIPE_API_KEY', 'sk_test_51L153aLHVthCw2smA7c2NqAfnvnpOzV03wSynsJXtDp97wodZqpkyDL2AOuW6ZeZLVVivEkUv0Oodbq4cSvMzl7400k13X30Ep'),
    DepositPaymentSessionConfig::MODE => 'payment',
    DepositPaymentSessionConfig::SUCCESS_URL => 'https://google.com/?session_id={CHECKOUT_SESSION_ID}',
    DepositPaymentSessionConfig::CANCEL_URL => 'https://google.com/?session_id={CHECKOUT_SESSION_ID}',
];
