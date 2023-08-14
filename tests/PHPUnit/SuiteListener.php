<?php

/** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Tests\PHPUnit;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use RuntimeException;
use Throwable;

/**
 * Has to be rely on the TestListener interface, because there is no hook which has startTestSuite support.
 */
final class SuiteListener implements TestListener
{
    private ?Connection $connection = null;

    /**
     * @param TestSuite<Test> $suite
     */
    public function startTestSuite(TestSuite $suite): void
    {
        if (true === \in_array($suite->getName(), ['integration', 'api'], true)) {
            $app = require __DIR__.'/../../src/Infrastructure/Boilerplate/Laravel/bootstrap/app.php';

            $app->make(Kernel::class)->bootstrap();

            $exitCode = Artisan::call('migrate:refresh', [
                '--force' => true,
            ]);

            if (0 !== $exitCode) {
                throw new RuntimeException('Could not refresh database!');
            }

            /** @var DatabaseManager $db */
            $db = $app->get('db');

            $this->connection = $db->connection();
            $this->connection->reconnect();
            $this->storeTruncateSchemaFunction();
            $this->connection?->disconnect();
        }
    }

    public function startTest(Test $test): void
    {
        if (null !== $this->connection) {
            $this->connection->reconnect();

            $this->connection->getPdo()->exec("SELECT truncate_schema('public');");
        }
    }

    public function addError(Test $test, Throwable $t, float $time): void
    {
    }

    public function addWarning(Test $test, Warning $e, float $time): void
    {
    }

    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
    }

    public function addIncompleteTest(Test $test, Throwable $t, float $time): void
    {
    }

    public function addRiskyTest(Test $test, Throwable $t, float $time): void
    {
    }

    public function addSkippedTest(Test $test, Throwable $t, float $time): void
    {
    }

    /**
     * @param TestSuite<Test> $suite
     */
    public function endTestSuite(TestSuite $suite): void
    {
    }

    public function endTest(Test $test, float $time): void
    {
    }

    /**
     * @throws \Exception if there is currently no connection
     */
    private function storeTruncateSchemaFunction(): void
    {
        $this->connection?->getPdo()->exec(
            <<<'TRUNC'
                CREATE OR REPLACE FUNCTION truncate_schema(_schema character varying)
                  RETURNS void AS
                $BODY$
                declare
                    selectrow record;
                begin
                for selectrow in
                select 'TRUNCATE TABLE ' || quote_ident(_schema) || '.' ||quote_ident(t.table_name) || ' CASCADE;' as qry
                from (
                     SELECT table_name
                     FROM information_schema.tables
                     WHERE table_type = 'BASE TABLE' AND table_schema = _schema AND table_name != 'event_streams' AND table_name != 'migrations'
                     )t
                loop
                execute selectrow.qry;
                end loop;
                end;
                $BODY$
                  LANGUAGE plpgsql
                TRUNC
        ) ?? throw new \Exception('no connection!');
    }
}
