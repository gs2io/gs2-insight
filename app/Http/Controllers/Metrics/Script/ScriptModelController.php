<?php

namespace App\Http\Controllers\Metrics\Script;

use App\Domain\Gs2Script\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ScriptModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $scriptModelName = $request->scriptModelName;

        $scriptModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->scriptModel($scriptModelName);

        return view('metrics/service/script/scriptModel')
            ->with('scriptModel', $scriptModel);
    }
}
