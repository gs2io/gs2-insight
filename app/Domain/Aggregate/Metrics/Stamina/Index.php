<?php

namespace App\Domain\Aggregate\Metrics\Stamina;

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

        $result = (new ConsumeStamina(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new RaiseMaxValue(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new RecoverStamina(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new SetMaxValue(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new SetMaxValueByStatus(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new SetRecoverInterval(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new SetRecoverIntervalByStatus(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new SetRecoverValue(
            $this->period,
            $this->datasetName,
            $this->credentials,
        ))->load();
        $totalBytesProcessed += $result->totalBytesProcessed;

        $result = (new SetRecoverValueByStatus(
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
