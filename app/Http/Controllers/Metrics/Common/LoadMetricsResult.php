<?php

namespace App\Http\Controllers\Metrics\Common;

class LoadMetricsResult {

    public array $keys;
    public array $categories;
    public array $metrics;

    public function __construct(
        array $keys,
        array $categories,
        array $metrics,
    )
    {
        $this->keys = $keys;
        $this->categories = $categories;
        $this->metrics = $metrics;
    }
}
