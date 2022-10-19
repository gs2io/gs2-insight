<?php

namespace Tests\Unit;

use App\Domain\Aggregate\Timelines;
use App\Domain\GcpDomain;
use DateInterval;
use DatePeriod;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $gcp = (new GcpDomain())->model();
        $period = new DatePeriod(
            $gcp->beginAt->setTimezone('UTC'),
            DateInterval::createFromDateString("1 second"),
            $gcp->endAt->setTimezone('UTC'),
        );

        $timelines = new Timelines(
            $period,
            $gcp->datasetName,
            $gcp->credentials,
        );
        $timelines->load()->totalBytesProcessed;
    }
}
