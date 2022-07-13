<?php

namespace App\Domain\Aggregate\Metrics\Gateway;

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
        $service = 'gateway';

        $totalBytesProcessed = 0;

        $grns = \App\Models\Grn::query()
            ->where('parent', "grn:gateway")
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
                    ['webSocketSession', 'item.name'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                ),
                'webSocketSession',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $webSocketSessionModels = GrnLoader::load(
                $this->createClient(),
                new \App\Models\Grn([
                    'grn' => "{$grn['grn']}:user:$userId",
                    'parent' => $grn['grn'],
                    'key' => $userId,
                ]),
                GrnLoader::buildQuery(
                    $service,
                    'webSocketSessionName',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                        'userId' => $userId,
                    ],
                ),
                'webSocketSession',
            );
            $totalBytesProcessed += $webSocketSessionModels->totalBytesProcessed;
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
