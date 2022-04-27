<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Stamina\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StaminaController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('stamina/namespace')
            ->with("namespace", $namespace);
    }

    public static function stamina(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $staminaModelName = $request->staminaModelName;

        $stamina = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->stamina($staminaModelName);

        return view('stamina/stamina')
            ->with("stamina", $stamina);
    }

    public static function consume(
        Request $request,
    ): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $staminaModelName = $request->staminaModelName;
        $userId = $request->userId;
        $consumeValue = $request->consumeValue;

        try {
            (new PlayerDomain($userId))->stamina()
                ->namespace($namespaceName)
                ->user($userId)
                ->stamina($staminaModelName)
                ->consume($consumeValue);
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=stamina");
    }

    public static function recover(
        Request $request,
    ): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $staminaModelName = $request->staminaModelName;
        $userId = $request->userId;
        $recoverValue = $request->recoverValue;

        try {
            (new PlayerDomain($userId))->stamina()
                ->namespace($namespaceName)
                ->user($userId)
                ->stamina($staminaModelName)
                ->recover($recoverValue);
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=stamina");
    }
}
