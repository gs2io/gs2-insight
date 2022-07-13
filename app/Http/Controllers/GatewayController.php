<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Gateway\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('gateway/namespace')
            ->with("namespace", $namespace);
    }

    public static function disconnect(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;

        $webSocketSession = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->webSocketSession();

        return redirect()->to("/players/$userId?mode=gateway");
    }
}
