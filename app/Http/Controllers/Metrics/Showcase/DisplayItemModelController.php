<?php

namespace App\Http\Controllers\Metrics\Showcase;

use App\Domain\Gs2Showcase\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DisplayItemModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $showcaseModelName = $request->showcaseModelName;
        $displayItemId = $request->displayItemId;

        $displayItemModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->showcaseModel($showcaseModelName)
            ->displayItemModel($displayItemId);

        return view('metrics/service/showcase/displayItemModel')
            ->with('displayItemModel', $displayItemModel);
    }
}
