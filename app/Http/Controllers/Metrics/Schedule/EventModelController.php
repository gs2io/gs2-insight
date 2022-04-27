<?php

namespace App\Http\Controllers\Metrics\Schedule;

use App\Domain\Gs2Schedule\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EventModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $eventModelName = $request->eventModelName;

        $eventModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->eventModel($eventModelName);

        return view('metrics/service/schedule/eventModel')
            ->with('eventModel', $eventModel);
    }
}
