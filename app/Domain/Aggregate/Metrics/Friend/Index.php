<?php

namespace App\Domain\Aggregate\Metrics\Friend;

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

        $result = (new AcceptRequest(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new Follow(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new RejectRequest(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new SendRequest(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new Unfollow(
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
