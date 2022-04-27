<?php

namespace App\Domain\Aggregate\Metrics\Datastore;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\MetricsLoader;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

class Download extends AbstractMetrics
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

        $service = 'datastore';
        $method = 'download';

        $totalBytesProcessed = 0;

        $namespaces = MetricsLoader::loadCount(
            $this->createClient(),
            $service,
            $this->table(),
            $this->timeRange(),
            $method,
            [
                'namespaceName',
            ],
            [],
            [
                'prepareDownload',
                'prepareDownloadByUserId',
                'prepareDownloadByGeneration',
                'prepareDownloadByGenerationByUserId',
                'prepareDownloadOwnData',
                'prepareDownloadByUserIdAndDataObjectName',
                'prepareDownloadOwnDataByGeneration',
                'prepareDownloadByUserIdAndDataObjectNameAndGeneration',
            ],
        );
        $totalBytesProcessed += $namespaces->totalBytesProcessed;
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
