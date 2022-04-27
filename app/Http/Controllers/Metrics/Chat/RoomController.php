<?php

namespace App\Http\Controllers\Metrics\Chat;

use App\Domain\Gs2Chat\ServiceDomain;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public static function index(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $roomName = $request->roomName;

        $room = (new ServiceDomain())
            ->namespace($namespaceName)
            ->room($roomName);

        return view('metrics/service/chat/room')
            ->with('room', $room);
    }
}
