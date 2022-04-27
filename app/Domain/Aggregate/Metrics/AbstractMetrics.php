<?php

namespace App\Domain\Aggregate\Metrics;

use App\Domain\Aggregate\AbstractAggregate;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

abstract class AbstractMetrics extends AbstractAggregate
{
    #[Pure] protected function __construct(
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

    protected function table($tableName = null): string
    {
        return parent::table("Invoke");
    }
}
