<?php

namespace App\Http\Controllers;

use App\Domain\Aggregate\Timelines;
use App\Domain\PlayerDomain;
use App\Domain\TimelineDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public static function info(
        Request $request,
    ): View
    {
        $userId = $request->userId;
        $transactionId = $request->transactionId;

        $timeline = new TimelineDomain(
            $userId,
            $transactionId,
        );
        $event = $timeline->model();

        if ($event->isAccessLog()) {
            return view('timeline/components/accessLog')
                ->with("timeline", $timeline)
                ->with("event", $event)
                ->with("accessLog", $timeline->accessLog());
        } else if ($event->isIssueStampSheetLog()) {
            return view('timeline/components/issueStampSheetLog')
                ->with("timeline", $timeline)
                ->with("event", $event)
                ->with("issueStampSheetLog", $timeline->issueStampSheetLog())
                ->with("executeStampSheetLog", $timeline->executeStampSheetLog())
                ->with("executeStampTaskLogs", $timeline->executeStampTaskLogs());
        }
        return view('notFound');
    }
}
