<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Lottery\ServiceDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LotteryController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('lottery/namespace')
            ->with("namespace", $namespace);
    }

    public static function lottery(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $lotteryModelName = $request->lotteryModelName;

        $lottery = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->lottery($lotteryModelName);

        return view('lottery/lottery')
            ->with("lottery", $lottery);
    }

    public static function draw(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $lotteryModelName = $request->lotteryModelName;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->lottery($lotteryModelName)
                ->draw();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=lottery");
    }
}
