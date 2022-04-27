<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Money\ServiceDomain;
use App\Domain\Gs2Money\WalletDomain;
use App\Domain\PlayerDomain;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MoneyController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('money/namespace')
            ->with("namespace", $namespace);
    }

    public static function wallet(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $slot = $request->slot;

        $wallet = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->wallet($slot);

        return view('money/wallet')
            ->with("wallet", $wallet);
    }

    public static function withdraw(
        Request $request,
    ): RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $slot = $request->slot;
        $count = $request->count;

        (new PlayerDomain($userId))
            ->money()
            ->namespace(
                $namespaceName,
            )->user(
                $userId,
            )->wallet(
                $slot,
            )->withdraw(
                $count,
            );

        return redirect()->to("/players/$userId?mode=money");
    }

    public static function deposit(
        Request $request,
    ): RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $slot = $request->slot;
        $price = $request->price;
        $count = $request->count;

        (new PlayerDomain($userId))
            ->money()
            ->namespace(
                $namespaceName,
            )->user(
                $userId,
            )->wallet(
                $slot,
            )->deposit(
                $price,
                $count,
            );

        return redirect()->to("/players/$userId?mode=money");
    }
}
