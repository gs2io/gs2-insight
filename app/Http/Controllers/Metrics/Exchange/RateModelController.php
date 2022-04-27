<?php

namespace App\Http\Controllers\Metrics\Exchange;

use App\Domain\Gs2Exchange\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RateModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $rateModelName = $request->rateModelName;

        $rateModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->rateModel($rateModelName);

        return view('metrics/service/exchange/rateModel')
            ->with('rateModel', $rateModel);
    }
}
