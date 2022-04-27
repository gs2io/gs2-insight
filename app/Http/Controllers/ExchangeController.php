<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Exchange\ServiceDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('exchange/namespace')
            ->with("namespace", $namespace);
    }

    public static function rate(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $rateModelName = $request->rateModelName;

        $rate = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->rate($rateModelName);

        return view('exchange/rate')
            ->with("rate", $rate);
    }

    public static function exchange(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $rateModelName = $request->rateModelName;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->rate($rateModelName)
                ->exchange();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=exchange");
    }
}
