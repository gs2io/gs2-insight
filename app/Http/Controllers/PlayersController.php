<?php

namespace App\Http\Controllers;

use App\Domain\Aggregate\Players;
use App\Domain\Aggregate\Timelines;
use App\Domain\GcpDomain;
use App\Domain\PlayerDomain;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlayersController extends Controller
{
    public static function list(Request $request): View
    {
        $userId = $request->userId;
        $purchasedAmountMin = $request->purchasedAmountMin ?? 0;
        $purchasedAmountMax = $request->purchasedAmountMax ?? PHP_FLOAT_MAX;

        return view('players')
            ->with("players", PlayerDomain::list(
                $userId,
                $purchasedAmountMin,
                $purchasedAmountMax,
            )->simplePaginate(10));
    }

    public static function info(Request $request): View | RedirectResponse
    {
        $userId = $request->userId;

        $gcp = (new GcpDomain())->model();
        $player = (new PlayerDomain($userId))->model();
        if($player->isNeedFetch($gcp->beginAt, $gcp->endAt)) {
            return redirect("/gcp/load/$userId");
        }

        return view('players/index')
            ->with("player", new PlayerDomain($userId));
    }

    public static function reload(Request $request): View | RedirectResponse
    {
        $userId = $request->userId;

        return redirect("/gcp/load/$userId");
    }
}
