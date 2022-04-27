<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Showcase\ServiceDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShowcaseController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('showcase/namespace')
            ->with("namespace", $namespace);
    }

    public static function showcase(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $showcaseModelName = $request->showcaseModelName;

        $showcase = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->showcase($showcaseModelName);

        return view('showcase/showcase')
            ->with("showcase", $showcase);
    }

    public static function displayItem(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $showcaseModelName = $request->showcaseModelName;
        $displayItemId = $request->displayItemId;

        $displayItem = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->showcase($showcaseModelName)
            ->displayItem($displayItemId);

        return view('showcase/displayItem')
            ->with("displayItem", $displayItem);
    }

    public static function buy(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $showcaseModelName = $request->showcaseModelName;
        $displayItemId = $request->displayItemId;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->showcase($showcaseModelName)
                ->displayItem($displayItemId)
                ->buy();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=showcase");
    }
}
