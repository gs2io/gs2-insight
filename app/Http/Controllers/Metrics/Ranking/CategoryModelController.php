<?php

namespace App\Http\Controllers\Metrics\Ranking;

use App\Domain\Gs2Ranking\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryModelController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $categoryModelName = $request->categoryModelName;

        $categoryModel = (new ServiceDomain())
            ->namespace($namespaceName)
            ->categoryModel($categoryModelName);

        return view('metrics/service/ranking/categoryModel')
            ->with('categoryModel', $categoryModel);
    }
}
