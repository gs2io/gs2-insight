<?php

namespace App\Console\Commands;

use App\Domain\GcpDomain;
use App\Http\Controllers\GcpController;
use DateInterval;
use DatePeriod;
use Illuminate\Console\Command;

class PopulateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importing data from BigQuery to a local database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $begin = microtime(true);

        ini_set("memory_limit", "16G");

        $gcp = (new GcpDomain())->model();
        if ($gcp != null) {
            $totalBytesProcessed = 0;

            $currentStatus = "Ranking";
            while ($currentStatus != null) {
                printf("Fetching...  %s\n", $currentStatus);
                $result = GcpController::loadImpl(
                    $currentStatus,
                    $totalBytesProcessed,
                );
                $currentStatus = $result["nextStatus"]?->toString();
                $totalBytesProcessed += $result["totalBytesProcessed"];
                break;
            }

            $elapsed = microtime(true) - $begin;
            printf("Processing time:  %.3f sec\n", $elapsed);
            printf("Total Bytes Processed: %s bytes\n", number_format($totalBytesProcessed));
        }
        return 0;
    }
}
