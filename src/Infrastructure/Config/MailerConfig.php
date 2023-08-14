<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Config;

final class MailerConfig
{
    public const EMAIL_TEMPLATE_FILENAME = 'deposit_payment';

    public const TYPE_MJML = 'mjml';
    public const TYPE_PLAIN = 'plain';

    public const TRACKED_CONTEXT = 'Reservation';
    public const TEMPLATE_IDENTIFIER = 'deposit_payment_session_created';
    public const TRACKED_DATA_RESERVATION_ID = 'reservation_id';
    public const TRACKED_DATA_PROSPECT_ID = 'prospect_id';
    public const TRACKED_DATA_UNIT_IDS = 'unit_ids';
}
