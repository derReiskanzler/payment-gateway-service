<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JetBrains\PhpStorm\NoReturn;

class RetryFailedProjections extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'projections:retry';

    /**
     * The console command description.
     */
    protected $description = 'Retry failed projections';

    /**
     * Execute the console command.
     */
    #[NoReturn]
    public function handle(): void
    {
        if (false === Schema::hasTable('failed_jobs')) {
            echo 'there is no failed jobs table configured.'.\PHP_EOL;
            exit(1);
        }

        $jobs = DB::table('failed_jobs')->select('id')->where('queue', '=', 'domain-events')->get()->toArray();

        if (empty($jobs)) {
            echo 'there are no failed projections to retry.'.\PHP_EOL;
            exit(0);
        }

        $jobs = array_map(static fn ($job) => $job->id, $jobs);
        $retryArgument = implode(' ', $jobs);

        Artisan::call('queue:retry '.$retryArgument);

        exit(0);
    }
}
