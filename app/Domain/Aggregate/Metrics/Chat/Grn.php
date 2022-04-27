<?php

namespace App\Domain\Aggregate\Metrics\Chat;

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
        $service = 'chat';

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
            $rooms = GrnLoader::load(
                $this->createClient(),
                $namespace,
                GrnLoader::buildQuery(
                    $service,
                    'roomName',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespace["key"],
                    ],
                ),
                'room',
            );
            $totalBytesProcessed += $rooms->totalBytesProcessed;
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
