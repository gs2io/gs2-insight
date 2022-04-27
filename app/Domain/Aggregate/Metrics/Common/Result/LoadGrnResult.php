<?php

namespace App\Domain\Aggregate\Metrics\Common\Result;

class LoadGrnResult {

    public array $grns;
    public int $totalBytesProcessed;

    public function __construct(
        array $grns,
        int $totalBytesProcessed,
    )
    {
        $this->grns = $grns;
        $this->totalBytesProcessed = $totalBytesProcessed;
    }

}
