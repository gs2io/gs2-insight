<?php

namespace App\Domain\Aggregate\Metrics\Common\Result;

class LoadMetricsResult {

    public array $metrics;
    public int $totalBytesProcessed;

    public function __construct(
        array $metrics,
        int $totalBytesProcessed,
    )
    {
        $this->metrics = $metrics;
        $this->totalBytesProcessed = $totalBytesProcessed;
    }

}
