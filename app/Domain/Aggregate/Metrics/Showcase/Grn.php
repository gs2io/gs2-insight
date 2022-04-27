<?php

namespace App\Domain\Aggregate\Metrics\Showcase;

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
        $service = 'showcase';

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
            $showcaseModels = GrnLoader::load(
                $this->createClient(),
                $namespace,
                GrnLoader::buildQuery(
                    $service,
                    'showcaseName',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespace["key"],
                    ],
                ),
                'showcaseModel',
            );
            $totalBytesProcessed += $showcaseModels->totalBytesProcessed;
            foreach ($showcaseModels->grns as $showcaseModel) {
                $displayItems = GrnLoader::load(
                    $this->createClient(),
                    $showcaseModel,
                    GrnLoader::buildQuery(
                        $service,
                        'displayItemId',
                        $this->table(),
                        $this->timeRange(),
                        [
                            'namespaceName' => $namespace["key"],
                            'showcaseName' => $showcaseModel["key"],
                        ],
                    ),
                    'displayItemModel',
                );
                $totalBytesProcessed += $displayItems->totalBytesProcessed;
            }
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
