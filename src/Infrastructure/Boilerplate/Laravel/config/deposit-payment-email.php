<?php

declare(strict_types=1);

return [
    'deposit_payment_email_sender_email' => env('DEPOSIT_PAYMENT_EMAIL_SENDER_EMAIL', 'reservations@allmyhomes.com'),
    'deposit_payment_email_sender_name' => env('DEPOSIT_PAYMENT_EMAIL_SENDER_NAME', 'AllMyHomes'),
    'deposit_payment_email_subjects' => [
        'de' => env('DEPOSIT_PAYMENT_EMAIL_SUBJECT_DE', 'Zahlungsinformationen der Reservierungsgebühren'),
        'en' => env('DEPOSIT_PAYMENT_EMAIL_SUBJECT_EN', 'Deposit payment details'),
    ],
];
