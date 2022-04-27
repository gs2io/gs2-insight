<?php

namespace App\Http\Controllers\Metrics\Mission;

use App\Domain\Gs2Mission\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CounterModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $counterModelName = $request->counterModelName;

        $counterModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->counterModel($counterModelName);

        return view('metrics/service/mission/counterModel')
            ->with('counterModel', $counterModel);
    }
}
