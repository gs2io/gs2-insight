<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Quest\ServiceDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('quest/namespace')
            ->with("namespace", $namespace);
    }

    public static function questGroup(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $questGroupModelName = $request->questGroupModelName;

        $questGroup = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->questGroup($questGroupModelName);

        return view('quest/questGroup')
            ->with("questGroup", $questGroup);
    }

    public static function quest(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $questGroupModelName = $request->questGroupModelName;
        $questModelName = $request->questModelName;

        $quest = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->questGroup($questGroupModelName)
            ->quest($questModelName);

        return view('quest/quest')
            ->with("quest", $quest);
    }

    public static function start(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $questGroupModelName = $request->questGroupModelName;
        $questModelName = $request->questModelName;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->questGroup($questGroupModelName)
                ->quest($questModelName)
                ->start();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=quest");
    }

    public static function complete(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->progress()
                ->complete();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=quest");
    }

    public static function failed(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->progress()
                ->failed();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=quest");
    }

    public static function delete(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->progress()
                ->delete();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=quest");
    }

    public static function resetCompleted(Request $request): View|RedirectResponse
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $questGroupModelName = $request->questGroupModelName;

        try {
            (new ServiceDomain())
                ->namespace($namespaceName)
                ->user($userId)
                ->questGroup($questGroupModelName)
                ->complete()
                ->reset();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }
        return redirect()->to("/players/$userId?mode=quest");
    }

}
