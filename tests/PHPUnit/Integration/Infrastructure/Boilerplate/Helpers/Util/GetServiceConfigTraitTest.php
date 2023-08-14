<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Integration\Infrastructure\Boilerplate\Helpers\Util;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Traits\GetServiceConfigTrait;
use Tests\TestCase;

class TraitTest
{
    use GetServiceConfigTrait;
}

class GetServiceConfigTraitTest extends TestCase
{
    public const AMHCLIENT_SERVICES_CONFIG_PATH = 'amhclient.service';

    protected TraitTest $testClass;

    /**
     * @var mixed|null
     */
    protected mixed $helper = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testClass = new TraitTest();
    }

    public function testGetServiceConfigWithOnlyService(): void
    {
        $services = config(self::AMHCLIENT_SERVICES_CONFIG_PATH);

        foreach ($services as $service => $serviceConfig) {
            $configPath = self::AMHCLIENT_SERVICES_CONFIG_PATH.'.'.$service;
            $originalConfig = config($configPath);
            $returnedConfig = $this->testClass->getServiceConfig($service);
            static::assertSame($originalConfig, $returnedConfig);
        }
    }

    public function testGetServiceConfigWithSubsequentConfigs(): void
    {
        $services = config(self::AMHCLIENT_SERVICES_CONFIG_PATH);

        foreach ($services as $service => $serviceConfig) {
            foreach ($serviceConfig as $key => $value) {
                $configPath = self::AMHCLIENT_SERVICES_CONFIG_PATH.'.'.$service.'.'.$key;
                $originalConfig = config($configPath);
                $returnedConfig = $this->testClass->getServiceConfig($service.'.'.$key);
                static::assertSame($originalConfig, $returnedConfig);
            }
        }
    }
}
