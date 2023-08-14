<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure;

final class Stream
{
    /* producing */
    public const PAYMENT_GATEWAY_DEPOSIT_PAYMENT_SESSION_STREAM = 'payment_gateway-deposit_payment_session-stream';

    public const PAYMENT_GATEWAY_DEPOSIT_PAYMENT_EMAIL_STREAM = 'payment_gateway-deposit_payment_email-stream';

    /* consuming */
    public const PROJECT_INFORMATION_SELLABLE_PROJECT_UNIT_CONTENTS_STREAM = 'project_information-sellable_project_unit_contents-stream';

    public const RESERVATION_MANAGEMENT_RESERVATION_STREAM = 'reservation_management-reservation-stream';

    public const USER_USERS_STREAM = 'user-users-stream';

    public const KEYCLOAK_ADAPTER_PROSPECTS_STREAM = 'keycloak_adapter-prospects-stream';
}
