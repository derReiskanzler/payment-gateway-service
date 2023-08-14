<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations;

use DateTimeInterface;

class DeprecationConfiguration implements DeprecationConfigurationInterface
{
    /**
     * DeprecationConfiguration constructor.
     *
     * @param DateTimeInterface|bool|string $deprecation Deprecation Date & time or true
     * @param string                        $link        link of the documentation of deprecation
     * @param DateTimeInterface             $sunset      Sunset Date & time
     */
    public function __construct(
        private DateTimeInterface|bool|string $deprecation,
        private string $link,
        private DateTimeInterface $sunset
    ) {
    }

    public function getDeprecation(): string|bool
    {
        if (\is_bool($this->deprecation) && $this->deprecation) {
            return 'true';
        }
        if (!\is_bool($this->deprecation) && \is_string($this->deprecation)) {
            $deprecationAsDate = date_create_immutable($this->deprecation);
            if ($deprecationAsDate) {
                return date_format($deprecationAsDate, DateTimeInterface::RFC7231);
            }
        }

        return false;
    }

    public function getLink(): string
    {
        return trim($this->link);
    }

    public function getSunset(): string
    {
        return date_format($this->sunset, DateTimeInterface::RFC7231);
    }
}
