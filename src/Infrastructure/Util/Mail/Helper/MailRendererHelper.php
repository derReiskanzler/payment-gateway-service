<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\Mail\Helper;

use Illuminate\Support\Facades\View;

final class MailRendererHelper implements MailRendererHelperInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function render(string $view, array $data): string
    {
        return View::make($view, $data)->render();
    }
}
