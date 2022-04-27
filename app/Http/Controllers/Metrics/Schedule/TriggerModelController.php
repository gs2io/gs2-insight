<?php

namespace App\Http\Controllers\Metrics\Schedule;

use App\Domain\Gs2Schedule\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TriggerModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $triggerModelName = $request->triggerModelName;

        $triggerModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->triggerModel($triggerModelName);

        return view('metrics/service/schedule/triggerModel')
            ->with('triggerModel', $triggerModel);
    }
}
