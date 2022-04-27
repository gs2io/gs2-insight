<?php

namespace App\Http\Controllers\Metrics\Lottery;

use App\Domain\Gs2Lottery\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LotteryModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $lotteryModelName = $request->lotteryModelName;

        $lotteryModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->lotteryModel($lotteryModelName);

        return view('metrics/service/lottery/lotteryModel')
            ->with('lotteryModel', $lotteryModel);
    }
}
