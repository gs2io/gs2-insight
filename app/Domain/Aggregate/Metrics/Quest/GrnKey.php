<?php

namespace App\Domain\Aggregate\Metrics\Quest;

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
        $service = 'quest';

        $totalBytesProcessed = 0;

        $grns = \App\Models\Grn::query()
            ->where('parent', "grn:quest")
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
                    ['questGroupName', 'item.questGroupName'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                ),
                'questGroupModel',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $grns2 = \App\Models\Grn::query()
                ->where('parent', "grn:quest:namespace:{$namespaceName}")
                ->where('category', "questGroupModel")
                ->get();
            foreach ($grns2 as $grn2) {
                $questGroupModelName = $grn2->key;

                $grnKeys = GrnKeyLoader::load(
                    $this->createClient(),
                    new \App\Models\Grn([
                        'grn' => "{$grn['grn']}:user:$userId:questGroupModel:{$questGroupModelName}",
                        'parent' => $grn2['grn'],
                        'key' => $userId,
                    ]),
                    GrnKeyLoader::buildQuery(
                        $service,
                        ['questModel', 'questName', 'request'],
                        $this->table(),
                        $userId,
                        $this->timeRange(),
                        [
                            'namespaceName' => $namespaceName,
                            'questGroupName' => $questGroupModelName,
                        ],
                    ),
                    'questModel',
                );
                $totalBytesProcessed += $grnKeys->totalBytesProcessed;

                $grnKeys = GrnKeyLoader::load(
                    $this->createClient(),
                    new \App\Models\Grn([
                        'grn' => "{$grn['grn']}:user:$userId:questGroupModel:{$questGroupModelName}",
                        'parent' => $grn2['grn'],
                        'key' => $userId,
                    ]),
                    "
                        SELECT
                            key,
                            requestId,
                        FROM
                        (
                            SELECT
                                CONCAT('questModel:', SPLIT(JSON_EXTRACT_SCALAR(result, '$.item.questModelId'), ':')[OFFSET(9)]) as key,
                                requestId,
                            FROM
                                `{$this->table()}`
                            WHERE
                                {$this->timeRange()} AND
                                service = 'quest' AND
                                method IN ('end', 'endByUserId', 'endByStampSheet', 'endByStampTask') AND
                                userId = '$userId'
                                AND SPLIT(JSON_EXTRACT_SCALAR(result, '$.item.questModelId'), ':')[OFFSET(5)] = 'sample-quest'
                                AND SPLIT(JSON_EXTRACT_SCALAR(result, '$.item.questModelId'), ':')[OFFSET(7)] = 'group1'
                        )
                        WHERE
                            key IS NOT NULL AND
                            key <> 'null'
                    ",
                    'questModel',
                );
                $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            }
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
