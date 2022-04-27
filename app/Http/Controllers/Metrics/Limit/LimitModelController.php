<?php

namespace App\Http\Controllers\Metrics\Limit;

use App\Domain\Gs2Limit\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LimitModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $limitModelName = $request->limitModelName;

        $limitModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->limitModel($limitModelName);

        return view('metrics/service/limit/limitModel')
            ->with('limitModel', $limitModel);
    }
}
