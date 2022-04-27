<?php

namespace App\Domain\Aggregate\Metrics\Experience;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\MetricsLoader;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use App\Models\Metrics;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

class RankValueMap extends AbstractMetrics
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

        $service = 'experience';
        $method = 'addExperience';

        $totalBytesProcessed = 0;

        $namespaces = MetricsLoader::loadValueMap(
            $this->createClient(),
            $service,
            $this->table(),
            $this->timeRange(),
            'item.rankValue',
            $method,
            [
                'namespaceName',
                'experienceName',
            ],
        );
        $totalBytesProcessed += $namespaces->totalBytesProcessed;

        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
