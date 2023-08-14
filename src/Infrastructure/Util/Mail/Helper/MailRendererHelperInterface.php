<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\Mail\Helper;

interface MailRendererHelperInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function render(string $view, array $data): string;
}
