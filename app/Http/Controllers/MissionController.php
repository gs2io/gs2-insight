<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Mission\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('mission/namespace')
            ->with("namespace", $namespace);
    }

    public static function missionGroup(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $missionGroupModelName = $request->missionGroupModelName;

        $missionGroup = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->missionGroup($missionGroupModelName);

        return view('mission/missionGroup')
            ->with("missionGroup", $missionGroup);
    }

    public static function missionTask(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $missionGroupModelName = $request->missionGroupModelName;
        $missionTaskModelName = $request->missionTaskModelName;

        $missionTask = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->missionGroup($missionGroupModelName)
            ->missionTask($missionTaskModelName);

        return view('mission/missionTask')
            ->with("missionTask", $missionTask);
    }

    public static function counter(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $counterModelName = $request->counterModelName;

        $counter = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->counter($counterModelName);

        return view('mission/counter')
            ->with("counter", $counter);
    }

    public static function increase(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $counterModelName = $request->counterModelName;
        $increaseValue = $request->increaseValue;

        try {
            (new PlayerDomain($userId))
                ->mission()
                ->namespace(
                    $namespaceName,
                )->user(
                    $userId
                )->counter(
                    $counterModelName,
                )->increase(
                    $increaseValue,
                );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=mission");
    }

    public static function receive(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $missionGroupModelName = $request->missionGroupModelName;
        $missionTaskModelName = $request->missionTaskModelName;

        try {
            (new PlayerDomain($userId))
                ->mission()
                ->namespace(
                    $namespaceName,
                )->user(
                    $userId
                )->missionGroup(
                    $missionGroupModelName,
                )->missionTask(
                    $missionTaskModelName,
                )->receive();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=mission");
    }

    public static function resetCounter(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $counterModelName = $request->counterModelName;

        try {
            (new PlayerDomain($userId))
                ->mission()
                ->namespace(
                    $namespaceName,
                )->user(
                    $userId
                )->counter(
                    $counterModelName,
                )->reset();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=mission");
    }

    public static function resetComplete(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $missionGroupModelName = $request->missionGroupModelName;

        try {
            (new PlayerDomain($userId))
                ->mission()
                ->namespace(
                    $namespaceName,
                )->user(
                    $userId
                )->missionGroup(
                    $missionGroupModelName,
                )->complete()->reset();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=mission");
    }
}
