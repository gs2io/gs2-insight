<?php

namespace App\Domain\Gs2Friend;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Friend\Gs2FriendRestClient;
use Gs2\Friend\Model\FriendUser;
use Gs2\Friend\Model\Profile;
use Gs2\Friend\Request\DeleteFriendByUserIdRequest;
use Gs2\Friend\Request\GetProfileByUserIdRequest;
use Gs2\Friend\Request\UpdateProfileByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ProfileDomain extends BaseDomain {

    public UserDomain $user;
    public Profile|null $profile;

    public function __construct(
        UserDomain $user,
        Profile|null $profile = null,
    ) {
        $this->user = $user;
        $this->profile = $profile;
    }

    public function update(
        string|null $publicProfile,
        string|null $followerProfile,
        string|null $friendProfile,
    )
    {
        $this->gs2(
            function (Gs2RestSession $session) use ($publicProfile, $followerProfile, $friendProfile) {
                $client = new Gs2FriendRestClient(
                    $session,
                );
                $result = $client->updateProfileByUserId(
                    (new UpdateProfileByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withPublicProfile($publicProfile)
                        ->withFollowerProfile($followerProfile)
                        ->withFriendProfile($friendProfile)
                );
                return $result->getItem();
            }
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2FriendRestClient(
                        $session,
                    );
                    $result = $client->getProfileByUserId(
                        (new GetProfileByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                    );
                    return $result->getItem();
                }
            );
            $profile = new ProfileDomain(
                $this->user,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $profile = $this;
        }

        return view($view)
            ->with("profile", $profile);
    }

    public function controllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with("profile", $this);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'profile')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:friend:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}")
                            ->orWhere('grn', 'like', "grn:friend:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}%")
                            ->getQuery()
                    ),
                "grnKey",
                "transactionId",
                "=",
                "grnKey.requestId",
            )->orderByDesc(
                "timestamp"
            );
    }

    public function timelineView(
        string $view,
    ): View
    {
        return view($view)
            ->with("friend", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

}
