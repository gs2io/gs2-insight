<?php

namespace App\Domain\Aggregate\Metrics\Mission;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\MetricsLoader;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

class Receive extends AbstractMetrics
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

        $service = 'mission';
        $method = 'receive';

        $totalBytesProcessed = 0;

        $namespaces = MetricsLoader::loadCount(
            $this->createClient(),
            $service,
            $this->table(),
            $this->timeRange(),
            $method,
            [
                'namespaceName',
                'missionGroupName',
                'missionTaskName',
            ],
        );
        $totalBytesProcessed += $namespaces->totalBytesProcessed;

        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
