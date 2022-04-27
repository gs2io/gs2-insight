<?php

namespace App\Domain\Aggregate\Metrics\Common\Result;

class LoadGrnKeyResult {

    public array $grnKeys;
    public int $totalBytesProcessed;

    public function __construct(
        array $grnKeys,
        int $totalBytesProcessed,
    )
    {
        $this->grnKeys = $grnKeys;
        $this->totalBytesProcessed = $totalBytesProcessed;
    }

}
