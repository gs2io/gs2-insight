<?php

namespace App\Domain\Aggregate\Metrics\Chat;

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
        $service = 'chat';

        $totalBytesProcessed = 0;

        $grns = \App\Models\Grn::query()
            ->where('parent', "grn:chat")
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
                    ['room', 'roomName', 'request'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                ),
                'room',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $roomModels = GrnLoader::load(
                $this->createClient(),
                new \App\Models\Grn([
                    'grn' => "{$grn['grn']}:user:$userId",
                    'parent' => $grn['grn'],
                    'key' => $userId,
                ]),
                GrnLoader::buildQuery(
                    $service,
                    'roomName',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                        'userId' => $userId,
                    ],
                ),
                'room',
            );
            $totalBytesProcessed += $roomModels->totalBytesProcessed;
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
