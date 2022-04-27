<?php

namespace App\Domain\Aggregate\Metrics\Lottery;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\MetricsLoader;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

class Draw extends AbstractMetrics
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

        $service = 'lottery';
        $method = 'draw';

        $totalBytesProcessed = 0;

        $namespaces = MetricsLoader::loadCount(
            $this->createClient(),
            $service,
            $this->table(),
            $this->timeRange(),
            $method,
            [
                'namespaceName',
                'lotteryName',
            ],
        );
        $totalBytesProcessed += $namespaces->totalBytesProcessed;

        $namespaces = MetricsLoader::loadSum(
            $this->createClient(),
            $service,
            $this->table(),
            $this->timeRange(),
            'count',
            $method,
            [
                'namespaceName',
                'lotteryName',
            ],
        );
        $totalBytesProcessed += $namespaces->totalBytesProcessed;

        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
