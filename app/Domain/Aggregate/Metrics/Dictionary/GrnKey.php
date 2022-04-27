<?php

namespace App\Domain\Aggregate\Metrics\Dictionary;

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
        $service = 'dictionary';

        $totalBytesProcessed = 0;

        $grns = \App\Models\Grn::query()
            ->where('parent', "grn:dictionary")
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
                GrnKeyLoader::buildArrayQuery(
                    $service,
                    ['entryModel', 'entryModelNames'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                ),
                'entryModel',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $grnKeys = GrnKeyLoader::load(
                $this->createClient(),
                $grn,
                GrnKeyLoader::buildUserQuery(
                    $service,
                    $this->table(),
                    $userId,
                    ['reset', 'resetByUserId', 'resetByStampTask'],
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                ),
                'user',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $dataObjectModels = GrnLoader::load(
                $this->createClient(),
                new \App\Models\Grn([
                    'grn' => "{$grn['grn']}:user:$userId",
                    'parent' => $grn['grn'],
                    'key' => $userId,
                ]),
                GrnLoader::buildArrayQuery(
                    $service,
                    'entryModelNames',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                        'userId' => $userId,
                    ],
                ),
                'entryModel',
            );
            $totalBytesProcessed += $dataObjectModels->totalBytesProcessed;

        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
