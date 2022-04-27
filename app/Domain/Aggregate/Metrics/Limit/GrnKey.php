<?php

namespace App\Domain\Aggregate\Metrics\Limit;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\GrnKeyLoader;
use App\Domain\Aggregate\Metrics\Common\GrnLoader;
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
        $service = 'limit';

        $totalBytesProcessed = 0;

        $grns = \App\Models\Grn::query()
            ->where('parent', "grn:limit")
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
                    ['limitModel', 'item.limitName'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                ),
                'limitModel',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $grns2 = \App\Models\Grn::query()
                ->where('parent', "grn:limit:namespace:{$namespaceName}")
                ->where('category', "limitModel")
                ->get();
            foreach ($grns2 as $grn2) {
                $limitModelName = $grn2->key;

                $grnKeys = GrnKeyLoader::load(
                    $this->createClient(),
                    new \App\Models\Grn([
                        'grn' => "{$grn['grn']}:user:$userId:limitModel:{$limitModelName}",
                        'parent' => "{$grn['grn']}:user:$userId",
                        'key' => $userId,
                    ]),
                    GrnKeyLoader::buildQuery(
                        $service,
                        ['counter', 'counterName', 'request'],
                        $this->table(),
                        $userId,
                        $this->timeRange(),
                        [
                            'namespaceName' => $namespaceName,
                            'limitName' => $limitModelName,
                        ],
                    ),
                    'counter',
                );
                $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            }
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
