<?php

namespace App\Domain\Aggregate\Metrics\Experience;

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
        $service = 'experience';

        $totalBytesProcessed = 0;

        $grns = \App\Models\Grn::query()
            ->where('parent', "grn:experience")
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
                    ['experienceModel', 'item.experienceName'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                ),
                'experienceModel',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $grns2 = \App\Models\Grn::query()
                ->where('parent', "grn:experience:namespace:{$namespaceName}")
                ->where('category', "experienceModel")
                ->get();
            foreach ($grns2 as $grn2) {
                $experienceModelName = $grn2->key;

                $grnKeys = GrnKeyLoader::load(
                    $this->createClient(),
                    new \App\Models\Grn([
                        'grn' => "{$grn['grn']}:user:$userId:experienceModel:{$experienceModelName}",
                        'parent' => $grn2['grn'],
                        'key' => $userId,
                    ]),
                    GrnKeyLoader::buildQuery(
                        $service,
                        ['property', 'item.propertyId'],
                        $this->table(),
                        $userId,
                        $this->timeRange(),
                        [
                            'namespaceName' => $namespaceName,
                            'experienceName' => $experienceModelName,
                        ],
                    ),
                    'property',
                );
                $totalBytesProcessed += $grnKeys->totalBytesProcessed;


                $dataObjectModels = GrnLoader::load(
                    $this->createClient(),
                    new \App\Models\Grn([
                        'grn' => "{$grn['grn']}:user:$userId:experienceModel:{$experienceModelName}",
                        'parent' => $grn['grn'],
                        'key' => $userId,
                    ]),
                    GrnLoader::buildQuery(
                        $service,
                        'propertyId',
                        $this->table(),
                        $this->timeRange(),
                        [
                            'namespaceName' => $namespaceName,
                            'userId' => $userId,
                            'experienceName' => $experienceModelName,
                        ],
                    ),
                    'status',
                );
                $totalBytesProcessed += $dataObjectModels->totalBytesProcessed;
            }
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
