<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Chat\ServiceDomain;
use App\Domain\PlayerDomain;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('chat/namespace')
            ->with("namespace", $namespace);
    }

    public static function room(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $roomName = $request->roomName;

        $room = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->room($roomName);

        return view('chat/room')
            ->with("room", $room);
    }

    public static function add(
        Request $request,
    ): RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $roomName = $request->roomName;

        (new PlayerDomain($userId))
            ->chat()
            ->namespace(
                $namespaceName,
            )->user(
                $userId,
            )->subscribe(
                $roomName,
            )->add();

        return redirect()->to("/players/$userId?mode=chat");
    }

    public static function delete(
        Request $request,
    ): RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $roomName = $request->roomName;

        (new PlayerDomain($userId))
            ->chat()
            ->namespace(
                $namespaceName,
            )->user(
                $userId,
            )->subscribe(
                $roomName,
            )->delete();

        return redirect()->to("/players/$userId?mode=chat");
    }
}
