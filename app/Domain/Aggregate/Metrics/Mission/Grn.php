<?php

namespace App\Domain\Aggregate\Metrics\Mission;

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
        $service = 'mission';

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
            $missionGroupModels = GrnLoader::load(
                $this->createClient(),
                $namespace,
                GrnLoader::buildQuery(
                    $service,
                    'missionGroupName',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespace["key"],
                    ],
                ),
                'missionGroupModel',
            );
            $totalBytesProcessed += $missionGroupModels->totalBytesProcessed;
            foreach ($missionGroupModels->grns as $missionGroup) {
                $missionTaskModels = GrnLoader::load(
                    $this->createClient(),
                    $missionGroup,
                    GrnLoader::buildQuery(
                        $service,
                        'missionTaskName',
                        $this->table(),
                        $this->timeRange(),
                        [
                            'namespaceName' => $namespace["key"],
                            'missionGroupName' => $missionGroup["key"],
                        ],
                    ),
                    'missionTaskModel',
                );
                $totalBytesProcessed += $missionTaskModels->totalBytesProcessed;
            }
            $counterModels = GrnLoader::load(
                $this->createClient(),
                $namespace,
                GrnLoader::buildQuery(
                    $service,
                    'counterName',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespace["key"],
                    ],
                ),
                'counterModel',
            );
            $totalBytesProcessed += $counterModels->totalBytesProcessed;
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
