<?php

declare(strict_types=1);

namespace Allmyhomes\Domain\Prospect\ValueObject;

use Allmyhomes\Domain\ValueObject\Language;

final class ProspectSalutation
{
    private const MALE = 0;
    private const FEMALE = 1;
    private const OTHER = 2;

    public const MR_DE = 'Herr';
    public const MR_EN = 'Mr.';
    public const MRS_DE = 'Frau';
    public const MRS_EN = 'Mrs.';

    public static function fromInt(int $salutation): self
    {
        return new self($salutation);
    }

    private function __construct(private int $salutation)
    {
    }

    public function toInt(): int
    {
        return $this->salutation;
    }

    public function toStringByLanguage(Language $language): string
    {
        switch ($this->salutation) {
            case self::MALE:
                if (Language::DE == $language->toString()) {
                    return self::MR_DE;
                }

                return self::MR_EN;
            case self::FEMALE:
                if (Language::DE == $language->toString()) {
                    return self::MRS_DE;
                }

                return self::MRS_EN;
            case self::OTHER:
            default:
                return '';
        }
    }
}
