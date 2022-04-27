<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Limit\ServiceDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LimitController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('limit/namespace')
            ->with("namespace", $namespace);
    }

    public static function limit(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $limitModelName = $request->limitModelName;

        $limit = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->limit($limitModelName);

        return view('limit/limit')
            ->with("limit", $limit);
    }

    public static function counter(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $limitModelName = $request->limitModelName;
        $counterName = $request->counterName;

        $counter = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->limit($limitModelName)
            ->counter($counterName);

        return view('limit/counter')
            ->with("counter", $counter);
    }

    public static function increase(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $limitModelName = $request->limitModelName;
        $counterName = $request->counterName;
        $count = $request->count;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->limit($limitModelName)
                ->counter($counterName)
                ->increase($count);
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=limit");
    }

    public static function reset(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $limitModelName = $request->limitModelName;
        $counterName = $request->counterName;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->limit($limitModelName)
                ->counter($counterName)
                ->reset();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=limit");
    }
}
