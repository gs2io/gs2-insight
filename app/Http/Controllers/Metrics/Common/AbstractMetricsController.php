<?php

namespace App\Http\Controllers\Metrics\Common;

use App\Models\Metrics;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

abstract class AbstractMetricsController extends Controller {

    protected static function loadImpl(
        string $service,
        string $method,
        string $category,
        string $timeSpan,
        array $filter,
        array $options = [],
    ): View {
        $timeFormat = "%Y-%m-%d %H";
        if ($timeSpan == 'hourly') {
            $timeFormat = "%Y-%m-%d %H";
        } else if ($timeSpan == 'daily') {
            $timeFormat = "%Y-%m-%d";
        } else if ($timeSpan == 'weekly') {
            $timeFormat = "%Y %U";
        } else if ($timeSpan == 'monthly') {
            $timeFormat = "%Y-%m";
        }

        $queryKey = "$service:$method:$category";
        if (in_array('queryKey', array_keys($options))) {
            $queryKey = $options['queryKey'];
        }
        foreach (array_keys($filter) as $key) {
            if (is_null($filter[$key])) {
                break;
            }
            $queryKey .= ":$key:{$filter[$key]}";
        }

        $result = Metrics::query()
            ->select(
                'key',
                DB::raw("DATE_FORMAT(timestamp, '$timeFormat') as time"),
                DB::raw('SUM(value) as value'),
            )
            ->where('key', 'like', $queryKey . '%')
            ->groupBy('key', 'time')
            ->get();
        $keys = array_unique($result->map(function ($item) {
            return $item['time'];
        })->toArray());
        $categories = array_unique($result->map(function ($item) {
            $values = explode(':', $item['key']);
            return $values[count($values)-1];
        })->toArray());
        $metrics = [];
        foreach ($result as $item) {
            $key = $item['time'];
            $values = explode(':', $item['key']);
            $metrics[$key][$values[count($values)-1]] = $item['value'];
        }
        return view('metrics/index')
            ->with('category', "{$service}_{$method}_{$timeSpan}")
            ->with('keys', $keys)
            ->with('categories', $categories)
            ->with('metrics', $metrics);
    }

}
