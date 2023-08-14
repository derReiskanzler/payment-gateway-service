<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Util\Mail\Helper;

interface MailTranslationHelperInterface
{
    /**
     * @param array<string, mixed> $replace
     */
    public function get(string $key, array $replace = [], string $locale = null, bool $fallback = true): string;

    /**
     * @param array<string, mixed> $replace
     */
    public function choice(string $key, int $number, array $replace = [], string $locale = null): string;

    public function getLocale(): string;

    public function setLocale(string $locale): void;
}
