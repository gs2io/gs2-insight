<?php

namespace App\Http\Controllers;

use App\Domain\Gs2Friend\ServiceDomain;
use App\Domain\PlayerDomain;
use Gs2\Core\Exception\Gs2Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public static function namespace(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $namespace = (new ServiceDomain())
            ->namespace($namespaceName);

        return view('friend/namespace')
            ->with("namespace", $namespace);
    }

    public static function friend(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $targetUserId = $request->targetUserId;

        $friend = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->friend($targetUserId);

        return view('friend/friend')
            ->with("friend", $friend);
    }

    public static function follower(Request $request): View
    {
        $namespaceName = $request->namespaceName;
        $userId = $request->userId;
        $targetUserId = $request->targetUserId;

        $follower = (new ServiceDomain())
            ->namespace($namespaceName)
            ->user($userId)
            ->follower($targetUserId);

        return view('friend/follower')
            ->with("follower", $follower);
    }

    public static function follow(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $targetUserId = $request->targetUserId;

        try {
            (new PlayerDomain($userId))
                ->friend()
                ->namespace($namespaceName)
                ->user($userId)
                ->follower($targetUserId)
                ->follow();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=friend");
    }

    public static function unfollow(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $targetUserId = $request->targetUserId;

        try {
            (new PlayerDomain($userId))
                ->friend()
                ->namespace($namespaceName)
                ->user($userId)
                ->follower($targetUserId)
                ->unfollow();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=friend");
    }

    public static function sendRequest(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $targetUserId = $request->targetUserId;

        try {
            (new PlayerDomain($userId))
                ->friend()
                ->namespace($namespaceName)
                ->user($userId)
                ->sendRequest($targetUserId)
                ->send();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=friend");
    }

    public static function deleteRequest(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $targetUserId = $request->targetUserId;

        try {
            (new PlayerDomain($userId))
                ->friend()
                ->namespace($namespaceName)
                ->user($userId)
                ->sendRequest($targetUserId)
                ->delete();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=friend");
    }

    public static function acceptRequest(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $fromUserId = $request->fromUserId;

        try {
            (new PlayerDomain($userId))
                ->friend()
                ->namespace($namespaceName)
                ->user($userId)
                ->receiveRequest($fromUserId)
                ->accept();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=friend");
    }

    public static function rejectRequest(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $fromUserId = $request->fromUserId;

        try {
            (new PlayerDomain($userId))
                ->friend()
                ->namespace($namespaceName)
                ->user($userId)
                ->receiveRequest($fromUserId)
                ->reject();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=friend");
    }

    public static function deleteFriend(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $targetUserId = $request->targetUserId;

        try {
            (new PlayerDomain($userId))
                ->friend()
                ->namespace($namespaceName)
                ->user($userId)
                ->friend($targetUserId)
                ->delete();
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=friend");
    }

    public static function updateProfile(
        Request $request,
    ): View|RedirectResponse
    {
        $userId = $request->userId;
        $namespaceName = $request->namespaceName;
        $publicProfile = $request->publicProfile;
        $followerProfile = $request->followerProfile;
        $friendProfile = $request->friendProfile;

        try {
            (new PlayerDomain($userId))
                ->friend()
                ->namespace($namespaceName)
                ->user($userId)
                ->profile()
                ->update(
                    $publicProfile,
                    $followerProfile,
                    $friendProfile
                );
        } catch (Gs2Exception $e) {
            return view('error')
                ->with("errors", $e);
        }

        return redirect()->to("/players/$userId?mode=friend");
    }

}
