<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class Language
{
    public const DE = 'de';
    public const EN = 'en';

    private const AVAILABLE_LANGUAGES = [
        self::DE,
        self::EN,
    ];

    public static function fromString(string $language): self
    {
        return new self($language);
    }

    private function __construct(private string $language)
    {
        Assert::inArray($this->language, self::AVAILABLE_LANGUAGES);
    }

    public function toString(): string
    {
        return $this->language;
    }
}
