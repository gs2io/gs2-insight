<?php

namespace App\Console\Commands;

use App\Domain\Aggregate\Metrics\Experience\GrnKey;
use App\Domain\Aggregate\Metrics\Lottery\Index;
use App\Domain\Aggregate\Players;
use App\Domain\Aggregate\Timelines;
use App\Domain\GcpDomain;
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

        ini_set("memory_limit", "4G");

        $gcp = (new GcpDomain())->model();
        if ($gcp != null) {
            $startDate = date_create($gcp->beginAt);
            $endDate = date_create($gcp->endAt);
            $interval = DateInterval::createFromDateString("1 second");

            $datasetName = $gcp->datasetName;
            $credentials = $gcp->credentials;

            $totalBytesProcessed = 0;

//            $timelines = new Timelines(
//                new DatePeriod($startDate, $interval, $endDate),
//                $datasetName,
//                $credentials,
//            );
//            $timelines->load();
//            $timelines->loadDetail("e51726ee-71da-4539-a026-a8efd7a761f2");

//            $players = new Players(
//                new \DatePeriod($startDate, $interval, $endDate),
//                $datasetName,
//                $credentials,
//            );
//            $players->load();
//
//            $result = (new Index(
//                new DatePeriod($startDate, $interval, $endDate),
//                $datasetName,
//                $credentials,
//            ))->load();
//            $totalBytesProcessed += $result->totalBytesProcessed;

            $result = (new GrnKey(
                new DatePeriod($startDate, $interval, $endDate),
                $datasetName,
                $credentials,
            ))->load("8a5f966c-bd4f-4848-a036-8a3305ccd898");
            $totalBytesProcessed += $result->totalBytesProcessed;

            $elapsed = microtime(true) - $begin;
            printf("Processing time:  %.3f sec\n", $elapsed);
            printf("Total Bytes Processed: %s bytes\n", number_format($totalBytesProcessed));
        }
        return 0;
    }
}
