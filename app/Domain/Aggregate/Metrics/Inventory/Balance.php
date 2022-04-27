<?php

namespace App\Domain\Aggregate\Metrics\Inventory;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\MetricsLoader;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use App\Models\Metrics;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

class Balance extends AbstractMetrics
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

        $service = 'inventory';
        $method = 'balance';

        $totalBytesProcessed = 0;

        $namespaces = MetricsLoader::loadBalance(
            $this->createClient(),
            $service,
            $this->table(),
            $this->timeRange(),
            $method,
            [
                'namespaceName',
                'inventoryName',
                'itemName',
            ],
            'acquireCount',
            [
                'acquireItemSet',
                'acquireItemSetByUserId',
                'acquireItemSetByStampSheet',
                'acquireItemSetByStampTask',
            ],
            'consumeCount',
            [
                'consumeItemSet',
                'consumeItemSetByUserId',
                'consumeItemSetByStampSheet',
                'consumeItemSetByStampTask',
            ],
        );
        $totalBytesProcessed += $namespaces->totalBytesProcessed;

        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
