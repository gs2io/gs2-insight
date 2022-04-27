<?php

namespace App\Http\Controllers\Metrics\Experience;

use App\Domain\Gs2Experience\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ExperienceModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $experienceModelName = $request->experienceModelName;

        $experienceModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->experienceModel($experienceModelName);

        return view('metrics/service/experience/experienceModel')
            ->with('experienceModel', $experienceModel);
    }
}
