<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Functional\Infrastructure\Boilerplate\Laravel\App\Logging\Processors;

use Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\TraceIdProviderInterface;
use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Logging\Processors\TraceIdLoggingProcessor;
use Illuminate\Log\Logger;
use Mockery;
use Mockery\MockInterface;
use Monolog\Handler\TestHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;

class TraceIdLoggingProcessorTest extends TestCase
{
    private TraceIdLoggingProcessor $traceIdLoggingProcessor;

    protected function setUp(): void
    {
        /** @var MockInterface&TraceIdProviderInterface $traceIdProvider */
        $traceIdProvider = Mockery::mock(TraceIdProviderInterface::class)
            ->makePartial()
            ->allows('getTraceId')
            ->andReturns('m6v8t4fmhy3y1lzf3awfi6qgu28zu7v4')
            ->getMock();

        $this->traceIdLoggingProcessor = new TraceIdLoggingProcessor($traceIdProvider);
    }

    public function testMonologLoggingProcessor(): void
    {
        $record = [
            'context' => [],
            'level' => 550,
        ];
        $testHandler = new TestHandler();
        $monologLogger = new \Monolog\Logger('test channel', [$testHandler]);
        /** @var Logger&MockInterface $logger */
        $logger = $this->mockLoggerObject($monologLogger);

        $this->traceIdLoggingProcessor->__invoke($logger);
        $testHandler->handle($record);

        static::assertSame('m6v8t4fmhy3y1lzf3awfi6qgu28zu7v4', $testHandler->getRecords()[0]['extra']['x-b3-traceid']);
    }

    public function testNotMonologLoggingProcessor(): void
    {
        $testLogger = new TestLogger();
        $testLogger->log('info', 'testing log');
        /** @var Logger&MockInterface $logger */
        $logger = $this->mockLoggerObject($testLogger);

        $this->traceIdLoggingProcessor->__invoke($logger);

        static::assertTrue($testLogger->hasInfoThatContains('testing log'));
    }

    private function mockLoggerObject(LoggerInterface $logger): Logger|MockInterface
    {
        return Mockery::mock(Logger::class)
            ->makePartial()
            ->allows('getLogger')
            ->andReturns($logger)
            ->getMock();
    }
}
