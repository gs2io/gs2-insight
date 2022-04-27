<?php

namespace App\Domain\Aggregate\Metrics\Inventory;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\GrnLoader;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

class Grn extends AbstractMetrics
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

        $totalBytesProcessed = 0;

        $namespaces = GrnLoader::load(
            $this->createClient(),
            GrnLoader::rootGrn($service),
            GrnLoader::buildQuery(
                $service,
                'namespaceName',
                $this->table(),
                $this->timeRange(),
                [],
            ),
            'namespace',
        );
        $totalBytesProcessed += $namespaces->totalBytesProcessed;
        foreach ($namespaces->grns as $namespace) {
            $inventoryModels = GrnLoader::load(
                $this->createClient(),
                $namespace,
                GrnLoader::buildQuery(
                    $service,
                    'inventoryName',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespace["key"],
                    ],
                ),
                'inventoryModel',
            );
            $totalBytesProcessed += $inventoryModels->totalBytesProcessed;
            foreach ($inventoryModels->grns as $inventory) {
                $itemModels = GrnLoader::load(
                    $this->createClient(),
                    $inventory,
                    GrnLoader::buildQuery(
                        $service,
                        'itemName',
                        $this->table(),
                        $this->timeRange(),
                        [
                            'namespaceName' => $namespace["key"],
                            'inventoryName' => $inventory["key"],
                        ],
                    ),
                    'itemModel',
                );
                $totalBytesProcessed += $itemModels->totalBytesProcessed;
            }
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
