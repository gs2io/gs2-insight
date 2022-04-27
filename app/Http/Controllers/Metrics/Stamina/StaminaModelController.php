<?php

namespace App\Http\Controllers\Metrics\Stamina;

use App\Domain\Gs2Stamina\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StaminaModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $staminaModelName = $request->staminaModelName;

        $staminaModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->staminaModel($staminaModelName);

        return view('metrics/service/stamina/staminaModel')
            ->with('staminaModel', $staminaModel);
    }
}
