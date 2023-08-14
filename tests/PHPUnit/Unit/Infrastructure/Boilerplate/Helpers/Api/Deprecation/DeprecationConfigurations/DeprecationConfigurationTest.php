<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations\DeprecationConfiguration;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations\DeprecationConfigurationInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DeprecationConfigurationTest extends TestCase
{
    public function testClassInitialization(): void
    {
        $deprecationConfiguration = new DeprecationConfiguration(true, 'http://www.google.com', new DateTimeImmutable('2020-05-20 10:00:00'));

        static::assertInstanceOf(DeprecationConfigurationInterface::class, $deprecationConfiguration);
    }

    public function testDeprecationValueAsBoolean(): void
    {
        $deprecationConfiguration = new DeprecationConfiguration(true, 'http://www.google.com', new DateTimeImmutable('2020-05-20 10:00:00'));

        static::assertSame('true', $deprecationConfiguration->getDeprecation());
    }

    public function testDeprecationValueAsDateTime(): void
    {
        $deprecationConfiguration = new DeprecationConfiguration('2020-04-20 10:00:00', 'http://www.google.com', new DateTimeImmutable('2020-05-20 10:00:00'));

        static::assertSame('Mon, 20 Apr 2020 10:00:00 GMT', $deprecationConfiguration->getDeprecation());
    }

    public function testDeprecationInvalidBoolValue(): void
    {
        $deprecationConfiguration = new DeprecationConfiguration(false, 'http://www.google.com', new DateTimeImmutable('2020-05-20 10:00:00'));

        static::assertFalse($deprecationConfiguration->getDeprecation());
    }

    public function testDeprecationInvalidValue(): void
    {
        $deprecationConfiguration = new DeprecationConfiguration('anything', 'http://www.google.com', new DateTimeImmutable('2020-05-20 10:00:00'));

        static::assertFalse($deprecationConfiguration->getDeprecation());
    }

    public function testLinkValue(): void
    {
        $deprecationConfiguration = new DeprecationConfiguration('2020-04-20 10:00:00', 'http://www.google.com', new DateTimeImmutable('2020-05-20 10:00:00'));

        static::assertSame('http://www.google.com', $deprecationConfiguration->getLink());
    }

    public function testSunsetValue(): void
    {
        $deprecationConfiguration = new DeprecationConfiguration('2020-04-20 10:00:00', 'http://www.google.com', new DateTimeImmutable('2020-05-20 10:00:00'));

        static::assertSame('Wed, 20 May 2020 10:00:00 GMT', $deprecationConfiguration->getSunset());
    }
}
