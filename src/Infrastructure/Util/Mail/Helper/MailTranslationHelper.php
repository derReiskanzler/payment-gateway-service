<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\Mail\Helper;

use Illuminate\Support\Facades\Lang;

final class MailTranslationHelper implements MailTranslationHelperInterface
{
    /**
     * @param array<string, mixed> $replace
     */
    public function get(string $key, array $replace = [], string $locale = null, bool $fallback = true): string
    {
        return Lang::get($key, $replace, $locale, $fallback);
    }

    /**
     * @param array<string, mixed> $replace
     */
    public function choice(string $key, int $number, array $replace = [], string $locale = null): string
    {
        return Lang::choice($key, $number, $replace, $locale);
    }

    public function getLocale(): string
    {
        return Lang::getLocale();
    }

    public function setLocale(string $locale): void
    {
        Lang::setLocale($locale);
    }
}
