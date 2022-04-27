<?php

namespace App\Domain\Aggregate\Metrics\Mission;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\GrnKeyLoader;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

class GrnKey extends AbstractMetrics
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

    public function load(
        string $userId,
    ): LoadResult {
        $service = 'mission';

        $totalBytesProcessed = 0;

        $grns = \App\Models\Grn::query()
            ->where('parent', "grn:mission")
            ->where('category', "namespace")
            ->get();
        foreach ($grns as $grn) {
            $namespaceName = $grn->key;

            $grnKeys = GrnKeyLoader::load(
                $this->createClient(),
                new \App\Models\Grn([
                    'grn' => "{$grn['grn']}:user:$userId",
                    'parent' => $grn['grn'],
                    'key' => $userId,
                ]),
                GrnKeyLoader::buildQuery(
                    $service,
                    ['missionGroupModel', 'missionGroupName', 'request'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                ),
                'missionGroupModel',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $grns2 = \App\Models\Grn::query()
                ->where('parent', "grn:mission:namespace:{$namespaceName}")
                ->where('category', "missionGroupModel")
                ->get();
            foreach ($grns2 as $grn2) {
                $missionGroupModelName = $grn2->key;

                $grnKeys = GrnKeyLoader::load(
                    $this->createClient(),
                    new \App\Models\Grn([
                        'grn' => "{$grn['grn']}:user:$userId:missionGroupModel:{$missionGroupModelName}",
                        'parent' => $grn2['grn'],
                        'key' => $userId,
                    ]),
                    GrnKeyLoader::buildQuery(
                        $service,
                        ['missionTaskModel', 'missionTaskName', 'request'],
                        $this->table(),
                        $userId,
                        $this->timeRange(),
                        [
                            'namespaceName' => $namespaceName,
                            'missionGroupName' => $missionGroupModelName,
                        ],
                    ),
                    'missionTaskModel',
                );
                $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            }

            $grnKeys = GrnKeyLoader::load(
                $this->createClient(),
                new \App\Models\Grn([
                    'grn' => "{$grn['grn']}:user:$userId",
                    'parent' => $grn['grn'],
                    'key' => $userId,
                ]),
                GrnKeyLoader::buildQuery(
                    $service,
                    ['counterModel', 'item.name'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                ),
                'counterModel',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
