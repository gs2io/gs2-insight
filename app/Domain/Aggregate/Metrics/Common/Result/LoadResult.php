<?php

namespace App\Domain\Aggregate\Metrics\Common\Result;

class LoadResult {

    public int $totalBytesProcessed;

    public function __construct(
        int $totalBytesProcessed,
    )
    {
        $this->totalBytesProcessed = $totalBytesProcessed;
    }

}
