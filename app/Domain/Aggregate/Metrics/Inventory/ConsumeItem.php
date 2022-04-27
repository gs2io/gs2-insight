<?php

namespace App\Domain\Aggregate\Metrics\Inventory;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\MetricsLoader;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

class ConsumeItem extends AbstractMetrics
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
        $method = 'consumeItem';

        $totalBytesProcessed = 0;

        $namespaces = MetricsLoader::loadCount(
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
            [],
            [
                'consumeItemSet',
                'consumeItemSetByUserId',
                'consumeItemSetByStampSheet',
                'consumeItemSetByStampTask',
            ],
        );
        $totalBytesProcessed += $namespaces->totalBytesProcessed;

        $namespaces = MetricsLoader::loadSum(
            $this->createClient(),
            $service,
            $this->table(),
            $this->timeRange(),
            $method,
            'consumeCount',
            [
                'namespaceName',
                'inventoryName',
                'itemName',
            ],
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
