<?php

namespace App\Domain\Aggregate\Metrics\Showcase;

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
        $service = 'showcase';

        $totalBytesProcessed = 0;

        $grns = \App\Models\Grn::query()
            ->where('parent', "grn:showcase")
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
                    ['showcaseModel', 'item.name'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                ),
                'showcaseModel',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $grns2 = \App\Models\Grn::query()
                ->where('parent', "grn:showcase:namespace:{$namespaceName}")
                ->where('category', "showcaseModel")
                ->get();
            foreach ($grns2 as $grn2) {
                $showcaseModelName = $grn2->key;

                $grnKeys = GrnKeyLoader::load(
                    $this->createClient(),
                    new \App\Models\Grn([
                        'grn' => "{$grn['grn']}:user:$userId:showcaseModel:{$showcaseModelName}",
                        'parent' => $grn2['grn'],
                        'key' => $userId,
                    ]),
                    GrnKeyLoader::buildQuery(
                        $service,
                        ['displayItemModel', 'displayItemId', 'request'],
                        $this->table(),
                        $userId,
                        $this->timeRange(),
                        [
                            'namespaceName' => $namespaceName,
                            'showcaseName' => $showcaseModelName,
                        ],
                    ),
                    'displayItemModel',
                );
                $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            }
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
