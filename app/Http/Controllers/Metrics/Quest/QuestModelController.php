<?php

namespace App\Http\Controllers\Metrics\Quest;

use App\Domain\Gs2Quest\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class QuestModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $questGroupModelName = $request->questGroupModelName;
        $questModelName = $request->questModelName;

        $questModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->questGroupModel($questGroupModelName)
            ->questModel($questModelName);

        return view('metrics/service/quest/questModel')
            ->with('questModel', $questModel);
    }
}
