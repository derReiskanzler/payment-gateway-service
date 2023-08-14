<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Config;

final class DepositPaymentSessionConfig
{
    public const API_KEY = 'api_key';

    public const LINE_ITEMS = 'line_items';
    public const MODE = 'mode';
    public const SUCCESS_URL = 'success_url';
    public const CANCEL_URL = 'cancel_url';
    public const LOCALE = 'locale';
    public const EXPIRES_AT = 'expires_at';
    public const METADATA = 'metadata';

    public const AGENT_ID = 'agent_id';
    public const PROJECT_ID = 'project_id';
    public const PROSPECT_ID = 'prospect_id';
    public const RESERVATION_ID = 'reservation_id';
}
