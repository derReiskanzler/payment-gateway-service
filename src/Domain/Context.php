<?php

declare(strict_types=1);

namespace Allmyhomes\Domain;

final class Context
{
    public const NAME = 'PaymentGateway.';

    public const DEFAULT_TIME_FORMAT = 'Y-m-d\TH:i:s.u';

    public const DE_TIME_FORMAT = 'd.m.y';

    public const EN_TIME_FORMAT = 'd.m.y';

    public const MAX_RETRY_ATTEMPTS = 5;
}
