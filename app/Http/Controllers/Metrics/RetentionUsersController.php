<?php

namespace App\Http\Controllers\Metrics;

use App\Http\Controllers\Controller;
use App\Models\Metrics;
use DateInterval;
use DateTime;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class RetentionUsersController extends Controller
{
    public static function daily(Request $request): View
    {
        $startYmd = '2022-01-25';

        $keys = [];
        $start = DateTime::createFromFormat('Y-m-d', $startYmd);
        for ($i=0; $i<7; $i++) {
            $keys[] = $start->format('Y-m-d');
            $start->add(DateInterval::createFromDateString("1 days"));
        }
        $result = Metrics::query()
            ->where('category', 'like', 'retention_users:daily:2022-01-25%')
            ->get();
        $items = [];
        foreach ($result as $item) {
            $item->source = explode(':', $item->key)[0];
            $item->target = explode(':', $item->key)[1];
            $items[] = $item;
        }
        usort($items, function ($a, $b) {
            if ($a->source == $b->source) {
                return strcmp($a->target, $b->target);
            } else {
                return strcmp($b->source, $a->source);
            }
        });
        return view('metrics/retention_users/index')
            ->with('category', 'retention_users_daily')
            ->with('metrics', $items)
            ->with('startYmd', $startYmd)
            ->with('keys', $keys);
    }
}
