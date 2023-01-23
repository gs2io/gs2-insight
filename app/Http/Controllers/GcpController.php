<?php

namespace App\Http\Controllers;

use App\Domain\Aggregate\Metrics\ActiveUsers;
use App\Domain\Aggregate\Metrics\Version\GrnKey;
use App\Domain\Aggregate\Metrics\Version\Index;
use App\Domain\Aggregate\Players;
use App\Domain\Aggregate\Timelines;
use App\Domain\GcpDomain;
use App\Domain\PlayerDomain;
use App\Http\Controllers\Enums\LoadTarget;
use App\Jobs\LoadDetailJob;
use App\Jobs\LoadJob;
use App\Models\LoadStatus;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class GcpController extends Controller
{
    public static function index(Request $request): View
    {
        $gcp = (new GcpDomain())->model();
        if (is_null($gcp)) {
            return view('gcp/create')
                ->with("url", "gcp/create");
        } else {
            return view('gcp/info')
                ->with("url", "gcp/update")
                ->with("gcp", (new GcpDomain())->model());
        }
    }

    public static function create(Request $request): RedirectResponse
    {
        $datasetName = $request->datasetName;
        $startAt = $request->startAt;
        $endAt = $request->endAt;
        $credentials = $request->credentials;

        GcpDomain::create(
            $datasetName,
            (new DateTime($startAt, new DateTimeZone(date_default_timezone_get())))->getTimestamp(),
            (new DateTime($endAt, new DateTimeZone(date_default_timezone_get())))->getTimestamp(),
            $credentials,
        );

        return redirect()->to("/gcp/load");
    }

    public static function update(Request $request): RedirectResponse
    {
        $startAt = $request->startAt;
        $endAt = $request->endAt;

        GcpDomain::update(
            (new DateTime($startAt, new DateTimeZone(date_default_timezone_get())))->getTimestamp(),
            (new DateTime($endAt, new DateTimeZone(date_default_timezone_get())))->getTimestamp(),
        );

        return redirect()->to("/gcp/load");
    }

    public static function load(): \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector|View
    {
        $globalStatus = LoadStatus::query()->find("global");
        if ($globalStatus == null) {
            $globalStatus = LoadStatus::query()->create([
                "scope" => "global",
                "working" => "init",
                "progress" => 0.0,
                "totalBytesProcessed" => 0,
            ]);
        }
        if ($globalStatus["working"] == "init" && $globalStatus["progress"] == 0.0) {
            $statuses = LoadStatus::query()->where("scope", "like", "global:%");
            foreach ($statuses as $v) {
                $v->delete();
            }

            LoadJob::dispatch(LoadTarget::Player);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Timeline);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Account);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Chat);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Datastore);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Dictionary);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Exchange);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Experience);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Friend);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Gateway);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Inbox);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Inventory);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::JobQueue);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Limit);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Lottery);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Matchmaking);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Mission);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Money);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Quest);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Ranking);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Realtime);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Schedule);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Script);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Showcase);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Stamina);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadJob::dispatch(LoadTarget::Version);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");

            $globalStatus->update([
                "progress" => 0.1,
            ]);
        }

        $workingStatus = LoadStatus::query()->find("global:player");
        if ($workingStatus == null) {
            $workingStatus = LoadStatus::query()->create([
                "scope" => "global:player",
                "working" => "init",
                "progress" => 0.0,
                "totalBytesProcessed" => 0,
            ]);
        }

        if ($workingStatus["progress"] == 1) {
            return redirect("/players");
        }

        return view('gcp/load')
            ->with("gcp", (new GcpDomain())->model())
            ->with("globalStatus", $globalStatus)
            ->with("workingStatus", $workingStatus);
    }

    public static function loadDetail(Request $request): \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector|View
    {
        $globalStatus = LoadStatus::query()->find($request->userId);
        if ($globalStatus == null) {
            $globalStatus = LoadStatus::query()->create([
                "scope" => $request->userId,
                "working" => "init",
                "progress" => 0.0,
                "totalBytesProcessed" => 0,
            ]);
        }
        if ($globalStatus["working"] == "init" && $globalStatus["progress"] == 0.0) {
            $statuses = LoadStatus::query()->where("scope", "like", "$request->userId:%");
            foreach ($statuses as $v) {
                $v->delete();
            }

            LoadDetailJob::dispatch($request->userId, LoadTarget::Player);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Timeline);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Account);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Chat);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Datastore);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Dictionary);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Exchange);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Experience);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Friend);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Gateway);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Inbox);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Inventory);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::JobQueue);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Limit);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Lottery);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Matchmaking);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Mission);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Money);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Quest);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Ranking);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Realtime);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Schedule);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Script);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Showcase);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Stamina);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");
            LoadDetailJob::dispatch($request->userId, LoadTarget::Version);
            exec("cd /var/www/html && php artisan queue:work --once --timeout=3600 > /dev/null &");

            $globalStatus->update([
                "progress" => 0.1,
            ]);
        }

        $workingStatus = LoadStatus::query()->find("$request->userId:timeline");
        if ($workingStatus == null) {
            $workingStatus = LoadStatus::query()->create([
                "scope" => "$request->userId:timeline",
                "working" => "init",
                "progress" => 0.0,
                "totalBytesProcessed" => 0,
            ]);
        }

        if ($workingStatus["progress"] == 1) {
            return redirect("/players/$request->userId");
        }

        return view('gcp/load_detail')
            ->with("gcp", (new GcpDomain())->model())
            ->with("globalStatus", $globalStatus)
            ->with("workingStatus", $workingStatus);
    }
}
