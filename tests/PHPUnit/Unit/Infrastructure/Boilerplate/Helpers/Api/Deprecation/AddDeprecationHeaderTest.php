<?php

/** @noinspection PhpIncompatibleReturnTypeInspection */

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\Api\Deprecation;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\AddDeprecationHeader;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations\DeprecationConfigurationInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationValidators\DeprecationHeaderValidatorInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\ResponseDeprecationInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AddDeprecationHeaderTest extends TestCase
{
    public function testClassInitialization(): void
    {
        $mockedConfiguration = Mockery::mock(DeprecationConfigurationInterface::class);
        $mockedValidator = Mockery::mock(DeprecationHeaderValidatorInterface::class);
        $addDeprecationHeader = new AddDeprecationHeader($mockedConfiguration, $mockedValidator);

        static::assertInstanceOf(ResponseDeprecationInterface::class, $addDeprecationHeader);
    }

    public function testDeprecateResponseWithDeprecationHeader(): void
    {
        $mockedValidator = $this->getMockedValidator();
        $mockedConfiguration = $this->getMockedConfiguration();
        $addDeprecationHeader = new AddDeprecationHeader($mockedConfiguration, $mockedValidator);

        $mockedResponse = Mockery::mock(Response::class)->makePartial();
        $mockedResponse->headers = new ResponseHeaderBag([]);

        $response = $addDeprecationHeader->deprecate($mockedResponse);

        static::assertSame('Mon, 04 Jan 2020 12:00:00 GMT', $response->headers->get('deprecation'));
    }

    public function testDeprecateResponseWithLinkHeader(): void
    {
        $mockedValidator = $this->getMockedValidator();
        $mockedConfiguration = $this->getMockedConfiguration();
        $addDeprecationHeader = new AddDeprecationHeader($mockedConfiguration, $mockedValidator);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse->headers = new ResponseHeaderBag([]);

        $response = $addDeprecationHeader->deprecate($mockedResponse);

        static::assertSame('<http://www.google.com>; rel="deprecation"; type="application/vnd.oai.openapi"', $response->headers->get('link'));
    }

    public function testDeprecateResponseWithSunsetHeader(): void
    {
        $mockedValidator = $this->getMockedValidator();
        $mockedConfiguration = $this->getMockedConfiguration();
        $addDeprecationHeader = new AddDeprecationHeader($mockedConfiguration, $mockedValidator);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse->headers = new ResponseHeaderBag([]);

        $response = $addDeprecationHeader->deprecate($mockedResponse);

        static::assertSame('Mon, 04 May 2020 12:00:00 GMT', $response->headers->get('sunset'));
    }

    /**
     * @return MockInterface&DeprecationConfigurationInterface
     */
    private function getMockedConfiguration(): DeprecationConfigurationInterface
    {
        $mockedConfiguration = Mockery::mock(DeprecationConfigurationInterface::class)->makePartial();
        $mockedConfiguration->allows('getDeprecation')->andReturns('Mon, 04 Jan 2020 12:00:00 GMT');
        $mockedConfiguration->allows('getSunset')->andReturns('Mon, 04 May 2020 12:00:00 GMT');
        $mockedConfiguration->allows('getLink')->andReturns('http://www.google.com')->getMock();

        return $mockedConfiguration;
    }

    /**
     * @return MockInterface&DeprecationHeaderValidatorInterface
     */
    private function getMockedValidator(): DeprecationHeaderValidatorInterface
    {
        /* @phpstan-ignore-next-line */
        return Mockery::mock(DeprecationHeaderValidatorInterface::class)
            ->allows('validate')
            ->andReturns(true)
            ->getMock();
    }
}
