<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationValidators;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationConfigurations\DeprecationConfigurationInterface;
use Allmyhomes\Infrastructure\Boilerplate\Helpers\Api\Deprecation\DeprecationValidators\FullDeprecationHeaderValidator;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class FullDeprecationHeaderValidatorTest extends TestCase
{
    public function testClassInitialization(): void
    {
        $deprecationValidator = new FullDeprecationHeaderValidator();

        static::assertInstanceOf(FullDeprecationHeaderValidator::class, $deprecationValidator);
    }

    public function testValidateDeprecationWithFalseBool(): void
    {
        /** @var DeprecationConfigurationInterface|MockInterface $mockedConfiguration */
        $mockedConfiguration = Mockery::mock(DeprecationConfigurationInterface::class)
            ->makePartial()
            ->allows('getDeprecation')
            ->andReturns(false)
            ->getMock();
        $deprecationValidator = new FullDeprecationHeaderValidator();

        $this->expectException(InvalidArgumentException::class);

        $deprecationValidator->validate($mockedConfiguration);
    }

    public function testValidateSunsetWithOlderThanDeprecationDate(): void
    {
        /** @var DeprecationConfigurationInterface|MockInterface $mockedConfiguration */
        $mockedConfiguration = Mockery::mock(DeprecationConfigurationInterface::class)
            ->makePartial()
            ->allows('getSunset')
            ->andReturns('Sun, 19 Apr 2020 10:00:00 GMT')
            ->getMock();
        $mockedConfiguration->allows('getDeprecation')->andReturns('Mon, 20 Apr 2020 10:00:00 GMT');

        $deprecationValidator = new FullDeprecationHeaderValidator();

        $this->expectException(InvalidArgumentException::class);

        $deprecationValidator->validate($mockedConfiguration);
    }

    public function testValidateSunsetWithEqualOfDeprecationDate(): void
    {
        /** @var DeprecationConfigurationInterface|MockInterface $mockedConfiguration */
        $mockedConfiguration = Mockery::mock(DeprecationConfigurationInterface::class)
            ->makePartial()
            ->allows('getSunset')
            ->andReturns('Sun, 19 Apr 2020 10:00:00 GMT')
            ->getMock();
        $mockedConfiguration->allows('getDeprecation')->andReturns('Sun, 19 Apr 2020 10:00:00 GMT');

        $deprecationValidator = new FullDeprecationHeaderValidator();

        $this->expectException(InvalidArgumentException::class);

        $deprecationValidator->validate($mockedConfiguration);
    }

    public function testValidateSunsetWithDeprecationDateIsTrue(): void
    {
        /** @var DeprecationConfigurationInterface|MockInterface $mockedConfiguration */
        $mockedConfiguration = Mockery::mock(DeprecationConfigurationInterface::class)
            ->makePartial()
            ->allows('getSunset')
            ->andReturns('Sun, 19 Apr 2020 10:00:00 GMT')
            ->getMock();
        $mockedConfiguration->allows('getDeprecation')->andReturns('true');

        $deprecationValidator = new FullDeprecationHeaderValidator();

        $deprecationValidator->validate($mockedConfiguration);

        //no exception is thrown
        $this->addToAssertionCount(1);
    }
}
