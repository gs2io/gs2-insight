<?php

namespace App\Http\Controllers\Metrics\Showcase;

use App\Domain\Gs2Showcase\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShowcaseModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $showcaseModelName = $request->showcaseModelName;

        $showcaseModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->showcaseModel($showcaseModelName);

        return view('metrics/service/showcase/showcaseModel')
            ->with('showcaseModel', $showcaseModel);
    }
}
