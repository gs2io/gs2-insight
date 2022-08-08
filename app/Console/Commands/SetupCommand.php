<?php

namespace App\Console\Commands;

use App\Domain\GcpDomain;
use App\Http\Controllers\GcpController;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:setup {dataset} {credential} {begin} {end}';

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
        $datasetName = $this->argument('dataset');
        $credentials = $this->argument('credential');
        $startAt = DateTime::createFromFormat('Y-m-d\TH:i:s+',$this->argument('begin'));
        $endAt = DateTime::createFromFormat('Y-m-d\TH:i:s+',$this->argument('end'));

        GcpDomain::create(
            $datasetName,
            $startAt->getTimestamp(),
            $endAt->getTimestamp(),
            $credentials,
        );

        return 0;
    }
}
