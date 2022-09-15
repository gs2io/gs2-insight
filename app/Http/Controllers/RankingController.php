<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Ranking\ServiceDomain;
use App\Domain\PlayerDomain;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('ranking/namespace')
            ->with("namespace", $namespace);
    }

    public static function category(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $categoryModelName = $request->categoryModelName;

        $category = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->category($categoryModelName);

        return view('ranking/category')
            ->with("category", $category);
    }

    public static function calc(
        Request $request,
    ): RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $categoryModelName = $request->categoryModelName;

        (new PlayerDomain($userId))
            ->ranking()
            ->namespace(
                $namespaceName,
            )->user(
                $userId,
            )->category(
                $categoryModelName,
            )->calc();

        return redirect()->to("/players/$userId?mode=ranking");
    }
}
