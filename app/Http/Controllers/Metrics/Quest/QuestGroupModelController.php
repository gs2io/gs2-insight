<?php

namespace App\Http\Controllers\Metrics\Quest;

use App\Domain\Gs2Quest\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class QuestGroupModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $questGroupModelName = $request->questGroupModelName;

        $questGroupModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->questGroupModel($questGroupModelName);

        return view('metrics/service/quest/questGroupModel')
            ->with('questGroupModel', $questGroupModel);
    }
}
