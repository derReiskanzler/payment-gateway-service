<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure;

final class Projection
{
    /* producing */
    public const PRODUCE_DEPOSIT_PAYMENT_SESSION_PROJECTION = 'payment_gateway-produce_deposit_payment_session-projection';

    public const PRODUCE_DEPOSIT_PAYMENT_EMAIL_PROJECTION = 'payment_gateway-produce_deposit_payment_email-projection';

    /* consuming */
    public const UNITS_PROJECTION = 'payment_gateway-units-projection';

    public const RESERVATIONS_PROJECTION = 'payment_gateway-reservations-projection';

    public const PROSPECTS_PROJECTION = 'payment_gateway-prospects-projection';

    public const CREATE_DEPOSIT_PAYMENT_SESSION_PROJECTION = 'payment_gateway-create_deposit_payment_session-projection';

    public const RETRY_DEPOSIT_PAYMENT_SESSION_PROJECTION = 'payment_gateway-retry_deposit_payment_session-projection';

    public const SEND_DEPOSIT_PAYMENT_EMAIL_TO_PROSPECT_PROJECTION = 'payment_gateway-send_deposit_payment_email_to_prospect-projection';
}
