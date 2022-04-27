<?php

namespace App\Http\Controllers\Metrics\Mission;

use App\Domain\Gs2Mission\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MissionGroupModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $missionGroupModelName = $request->missionGroupModelName;

        $missionGroupModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->missionGroupModel($missionGroupModelName);

        return view('metrics/service/mission/missionGroupModel')
            ->with('missionGroupModel', $missionGroupModel);
    }
}
