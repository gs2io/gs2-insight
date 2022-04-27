<?php

namespace App\Http\Controllers\Metrics;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Metrics\Common\AbstractMetricsController;
use App\Models\Metrics;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ActiveUserController extends AbstractMetricsController
{
    public static function load(string $timeSpan): View
    {
        $service = 'general';
        $method = 'uniquePlayer';
        $category = 'count';

        return self::loadImpl(
            $service,
            $method,
            $category,
            $timeSpan,
            [],
            [
                'queryKey' => "{$service}:{$method}:{$category}:{$timeSpan}",
            ],
        );
    }
}
