<?php

namespace App\Domain\Aggregate\Metrics\Limit;

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
        $service = 'limit';

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
            $limitModels = GrnLoader::load(
                $this->createClient(),
                $namespace,
                GrnLoader::buildQuery(
                    $service,
                    'limitName',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespace["key"],
                    ],
                ),
                'limitModel',
            );
            $totalBytesProcessed += $limitModels->totalBytesProcessed;
            foreach ($limitModels->grns as $limitModel) {
                $counterModels = GrnLoader::load(
                    $this->createClient(),
                    $limitModel,
                    GrnLoader::buildQuery(
                        $service,
                        'counterName',
                        $this->table(),
                        $this->timeRange(),
                        [
                            'namespaceName' => $namespace["key"],
                            'limitName' => $limitModel["key"],
                        ],
                    ),
                    'counter',
                );
                $totalBytesProcessed += $counterModels->totalBytesProcessed;
            }
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
