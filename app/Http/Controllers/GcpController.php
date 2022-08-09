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
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    public static function loadImpl(
        string|null $currentStatus,
        int $totalBytesProcessed,
    ): array {

        $currentStatus = $currentStatus ? LoadTarget::valueOf($currentStatus) : LoadTarget::Initialize;
        $nextStatus = LoadTarget::Done;

        $gcp = (new GcpDomain())->model();
        $period = new DatePeriod(
            $gcp->beginAt->setTimezone('UTC'),
            DateInterval::createFromDateString("1 second"),
            $gcp->endAt->setTimezone('UTC'),
        );

        switch ($currentStatus) {
            case LoadTarget::Initialize;
                $nextStatus = LoadTarget::Timeline;
                break;
            case LoadTarget::Timeline;
                $timelines = new Timelines(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Player;
                break;
            case LoadTarget::Player;
                $players = new Players(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $players->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::ActiveUser;
                break;
            case LoadTarget::ActiveUser;
                $timelines = new ActiveUsers(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Account;
                break;

            case LoadTarget::Account;
                $timelines = new \App\Domain\Aggregate\Metrics\Account\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Chat;
                break;
            case LoadTarget::Chat;
                $timelines = new \App\Domain\Aggregate\Metrics\Chat\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Datastore;
                break;
            case LoadTarget::Datastore;
                $timelines = new \App\Domain\Aggregate\Metrics\Datastore\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Dictionary;
                break;
            case LoadTarget::Dictionary;
                $timelines = new \App\Domain\Aggregate\Metrics\Dictionary\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Exchange;
                break;
            case LoadTarget::Exchange;
                $timelines = new \App\Domain\Aggregate\Metrics\Exchange\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Experience;
                break;
            case LoadTarget::Experience;
                $timelines = new \App\Domain\Aggregate\Metrics\Experience\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Friend;
                break;
            case LoadTarget::Friend;
                $timelines = new \App\Domain\Aggregate\Metrics\Friend\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Gateway;
                break;
            case LoadTarget::Gateway;
                $timelines = new \App\Domain\Aggregate\Metrics\Gateway\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Inbox;
                break;
            case LoadTarget::Inbox;
                $timelines = new \App\Domain\Aggregate\Metrics\Inbox\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Inventory;
                break;
            case LoadTarget::Inventory;
                $timelines = new \App\Domain\Aggregate\Metrics\Inventory\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::JobQueue;
                break;
            case LoadTarget::JobQueue;
                $timelines = new \App\Domain\Aggregate\Metrics\JobQueue\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Limit;
                break;
            case LoadTarget::Limit;
                $timelines = new \App\Domain\Aggregate\Metrics\Limit\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Lottery;
                break;
            case LoadTarget::Lottery;
                $timelines = new \App\Domain\Aggregate\Metrics\Lottery\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Matchmaking;
                break;
            case LoadTarget::Matchmaking;
                $timelines = new \App\Domain\Aggregate\Metrics\Matchmaking\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Mission;
                break;
            case LoadTarget::Mission;
                $timelines = new \App\Domain\Aggregate\Metrics\Mission\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Money;
                break;
            case LoadTarget::Money;
                $timelines = new \App\Domain\Aggregate\Metrics\Money\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Quest;
                break;
            case LoadTarget::Quest;
                $timelines = new \App\Domain\Aggregate\Metrics\Quest\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Ranking;
                break;
            case LoadTarget::Ranking;
                $timelines = new \App\Domain\Aggregate\Metrics\Ranking\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Realtime;
                break;
            case LoadTarget::Realtime;
                $timelines = new \App\Domain\Aggregate\Metrics\Realtime\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Schedule;
                break;
            case LoadTarget::Schedule;
                $timelines = new \App\Domain\Aggregate\Metrics\Schedule\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Script;
                break;
            case LoadTarget::Script;
                $timelines = new \App\Domain\Aggregate\Metrics\Script\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Showcase;
                break;
            case LoadTarget::Showcase;
                $timelines = new \App\Domain\Aggregate\Metrics\Showcase\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Stamina;
                break;
            case LoadTarget::Stamina;
                $timelines = new \App\Domain\Aggregate\Metrics\Stamina\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Version;
                break;
            case LoadTarget::Version;
                $timelines = new Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load()->totalBytesProcessed;
                $nextStatus = LoadTarget::Done;
                break;

            case LoadTarget::Done;
                $nextStatus = null;
                break;
        }
        return [
            "currentStatus" => $currentStatus,
            "nextStatus" => $nextStatus,
            "totalBytesProcessed" => $totalBytesProcessed,
        ];
    }


    public static function load(Request $request): View
    {
        $result = self::loadImpl(
            $request->status,
            $request->totalBytesProcessed ?? 0
        );
        $currentStatus = $result["currentStatus"];
        $totalBytesProcessed = $result["totalBytesProcessed"];
        $nextStatus = $result["nextStatus"];

        return view('gcp/load')
            ->with("gcp", (new GcpDomain())->model())
            ->with("currentStatus", $currentStatus)
            ->with("totalBytesProcessed", $totalBytesProcessed)
            ->with("nextUrl", $nextStatus == null ? "/" : "/gcp/load?status={$nextStatus->toString()}&totalBytesProcessed={$totalBytesProcessed}");
    }

    public static function loadDetail(Request $request): View
    {
        $totalBytesProcessed = $request->totalBytesProcessed ?? 0;

        $userId = $request->userId;

        $currentStatus = $request->status ? LoadTarget::valueOf($request->status) : LoadTarget::Initialize;
        $nextStatus = LoadTarget::Done;

        $gcp = (new GcpDomain())->model();
        $period = new DatePeriod(
            $gcp->beginAt->setTimezone('UTC'),
            DateInterval::createFromDateString("1 second"),
            $gcp->endAt->setTimezone('UTC'),
        );

        switch ($currentStatus) {
            case LoadTarget::Initialize;
                $nextStatus = LoadTarget::Timeline;
                break;
            case LoadTarget::Timeline;
                $timelines = new Timelines(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->loadDetail($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Account;
                break;

            case LoadTarget::Account;
                $timelines = new \App\Domain\Aggregate\Metrics\Account\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Chat;
                break;
            case LoadTarget::Chat;
                $timelines = new \App\Domain\Aggregate\Metrics\Chat\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Datastore;
                break;
            case LoadTarget::Datastore;
                $timelines = new \App\Domain\Aggregate\Metrics\Datastore\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Dictionary;
                break;
            case LoadTarget::Dictionary;
                $timelines = new \App\Domain\Aggregate\Metrics\Dictionary\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Exchange;
                break;
            case LoadTarget::Exchange;
                $timelines = new \App\Domain\Aggregate\Metrics\Exchange\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Experience;
                break;
            case LoadTarget::Experience;
                $timelines = new \App\Domain\Aggregate\Metrics\Experience\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Friend;
                break;
            case LoadTarget::Friend;
                $timelines = new \App\Domain\Aggregate\Metrics\Friend\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Gateway;
                break;
            case LoadTarget::Gateway;
                $timelines = new \App\Domain\Aggregate\Metrics\Gateway\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Inbox;
                break;
            case LoadTarget::Inbox;
                $timelines = new \App\Domain\Aggregate\Metrics\Inbox\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Inventory;
                break;
            case LoadTarget::Inventory;
                $timelines = new \App\Domain\Aggregate\Metrics\Inventory\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::JobQueue;
                break;
            case LoadTarget::JobQueue;
                $timelines = new \App\Domain\Aggregate\Metrics\JobQueue\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Limit;
                break;
            case LoadTarget::Limit;
                $timelines = new \App\Domain\Aggregate\Metrics\Limit\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Lottery;
                break;
            case LoadTarget::Lottery;
                $timelines = new \App\Domain\Aggregate\Metrics\Lottery\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Matchmaking;
                break;
            case LoadTarget::Matchmaking;
                $timelines = new \App\Domain\Aggregate\Metrics\Matchmaking\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Mission;
                break;
            case LoadTarget::Mission;
                $timelines = new \App\Domain\Aggregate\Metrics\Mission\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Money;
                break;
            case LoadTarget::Money;
                $timelines = new \App\Domain\Aggregate\Metrics\Money\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Quest;
                break;
            case LoadTarget::Quest;
                $timelines = new \App\Domain\Aggregate\Metrics\Quest\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Ranking;
                break;
            case LoadTarget::Ranking;
                $timelines = new \App\Domain\Aggregate\Metrics\Ranking\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Realtime;
                break;
            case LoadTarget::Realtime;
                $timelines = new \App\Domain\Aggregate\Metrics\Realtime\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Schedule;
                break;
            case LoadTarget::Schedule;
                $timelines = new \App\Domain\Aggregate\Metrics\Schedule\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Script;
                break;
            case LoadTarget::Script;
                $timelines = new \App\Domain\Aggregate\Metrics\Script\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Showcase;
                break;
            case LoadTarget::Showcase;
                $timelines = new \App\Domain\Aggregate\Metrics\Showcase\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Stamina;
                break;
            case LoadTarget::Stamina;
                $timelines = new \App\Domain\Aggregate\Metrics\Stamina\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Version;
                break;
            case LoadTarget::Version;
                $timelines = new GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $totalBytesProcessed += $timelines->load($userId)->totalBytesProcessed;
                $nextStatus = LoadTarget::Done;
                break;

            case LoadTarget::Done;
                $player = (new PlayerDomain($userId))->model();
                $player->fetched($gcp->beginAt, $gcp->endAt);
                $nextStatus = null;
                break;
        }
        return view('gcp/load_detail')
            ->with("gcp", (new GcpDomain())->model())
            ->with("currentStatus", $currentStatus)
            ->with("totalBytesProcessed", $totalBytesProcessed)
            ->with("nextUrl", $nextStatus == null ? "/players/$userId" : "/gcp/load/$userId?status={$nextStatus->toString()}&totalBytesProcessed={$totalBytesProcessed}");
    }
}
