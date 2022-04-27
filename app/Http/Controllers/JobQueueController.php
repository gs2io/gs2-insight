<?php

namespace App\Http\Controllers;

use App\Domain\Gs2JobQueue\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobQueueController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('jobQueue/namespace')
            ->with("namespace", $namespace);
    }

    public static function job(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $jobName = $request->jobName;

        $job = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->job($jobName);

        return view('jobQueue/job')
            ->with("job", $job);
    }

    public static function run(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->run();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=jobQueue");
    }
}
