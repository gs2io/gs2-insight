<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Inbox\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Gs2\Inbox\Request\SendMessageByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('inbox/namespace')
            ->with("namespace", $namespace);
    }

    public static function message(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $messageName = $request->messageName;

        $message = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->message($messageName);

        return view('inbox/message')
            ->with("message", $message);
    }

    public static function read(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $messageName = $request->messageName;

        try {
            (new PlayerDomain($userId))
                ->inbox()
                ->namespace($namespaceName)
                ->user($userId)
                ->message($messageName)
                ->read();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=inbox");
    }

    public static function delete(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $messageName = $request->messageName;

        try {
            (new PlayerDomain($userId))
                ->inbox()
                ->namespace($namespaceName)
                ->user($userId)
                ->message($messageName)
                ->delete();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=inbox");
    }
}
