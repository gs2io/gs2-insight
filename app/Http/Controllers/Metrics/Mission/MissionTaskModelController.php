<?php

namespace App\Http\Controllers\Metrics\Mission;

use App\Domain\Gs2Mission\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MissionTaskModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $missionGroupModelName = $request->missionGroupModelName;
        $missionTaskModelName = $request->missionTaskModelName;

        $missionTaskModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->missionGroupModel($missionGroupModelName)
            ->missionTaskModel($missionTaskModelName);

        return view('metrics/service/mission/missionTaskModel')
            ->with('missionTaskModel', $missionTaskModel);
    }
}
