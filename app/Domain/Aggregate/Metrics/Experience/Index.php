<?php

namespace App\Domain\Aggregate\Metrics\Experience;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

class Index extends AbstractMetrics
{

    #[Pure] public function __construct(
        DatePeriod $period,
        string $datasetName,
        string $credentials,
    )
    {
        parent::__construct(
            $period,
            $datasetName,
            $credentials,
        );
    }

    public function load(): LoadResult {

        $totalBytesProcessed = 0;

        $result = (new Grn(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new AddExperience(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new AddRankCap(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new RankValueMap(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new SetExperience(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new SetRankCap(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
