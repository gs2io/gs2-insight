<?php

namespace App\Domain\Aggregate\Metrics\Account;

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

        $result = (new Authentication(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new CreateAccount(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new CreateTakeOver(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new DoTakeOver(
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
