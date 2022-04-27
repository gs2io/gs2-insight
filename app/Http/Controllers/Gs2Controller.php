<?php

namespace App\Http\Controllers;

use App\Domain\Aggregate\Metrics\ActiveUsers;
use App\Domain\Aggregate\Metrics\Version\GrnKey;
use App\Domain\Aggregate\Metrics\Version\Index;
use App\Domain\Aggregate\Players;
use App\Domain\Aggregate\Timelines;
use App\Domain\Gs2Domain;
use App\Domain\PlayerDomain;
use App\Http\Controllers\Enums\LoadTarget;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class Gs2Controller extends Controller
{
    public static function index(Request $request): View
    {
        $gs2 = (new Gs2Domain())->model();
        if (is_null($gs2)) {
            return view('gs2/create')
                ->with("url", "gs2/create");
        } else {
            return view('gs2/info')
                ->with("url", "gs2/update")
                ->with("gs2", (new Gs2Domain())->model());
        }
    }

    public static function create(Request $request): RedirectResponse
    {
        $clientId = $request->clientId;
        $clientSecret = $request->clientSecret;
        $region = $request->region;
        $permission = $request->permission;

        Gs2Domain::create(
            $clientId,
            $clientSecret,
            $region,
            $permission,
        );

        \Illuminate\Support\Facades\View::share('permission', $permission);

        return redirect()->to("/");
    }

    public static function update(Request $request): RedirectResponse
    {
        $region = $request->region;
        $permission = $request->permission;

        Gs2Domain::update(
            $region,
            $permission,
        );

        \Illuminate\Support\Facades\View::share('permission', $permission);

        return redirect()->to("/");
    }
}
