<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Account\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('account/namespace')
            ->with("namespace", $namespace);
    }

    public static function takeOver(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $type = $request->type;

        $takeOver = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->takeOver($type);

        return view('account/takeOver')
            ->with("takeOver", $takeOver);
    }

    public static function add(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $type = $request->type;
        $userIdentifier = $request->userIdentifier;
        $password = $request->password;

        try {
            (new PlayerDomain($userId))
                ->account()
                ->namespace(
                    $namespaceName,
                )->user(
                    $userId,
                )->takeOver(
                    $type,
                )->add(
                    $userIdentifier,
                    $password,
                );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=account");
    }

    public static function delete(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $type = $request->type;
        $userIdentifier = $request->userIdentifier;

        try {
            (new PlayerDomain($userId))
                ->account()
                ->namespace(
                    $namespaceName,
                )->user(
                    $userId,
                )->takeOver(
                    $type,
                )->delete(
                    $userIdentifier,
                );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=account");
    }

    public static function deleteDataOwner(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;

        try {
            (new PlayerDomain($userId))
                ->account()
                ->namespace(
                    $namespaceName,
                )->user(
                    $userId,
                )->dataOwner(
                )->delete(
                );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=account");
    }
}
