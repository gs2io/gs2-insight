<?php

namespace App\Domain\Gs2Friend;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Friend\Gs2FriendRestClient;
use Gs2\Friend\Model\FollowUser;
use Gs2\Friend\Request\FollowByUserIdRequest;
use Gs2\Friend\Request\GetFollowByUserIdRequest;
use Gs2\Friend\Request\GetFriendByUserIdRequest;
use Gs2\Friend\Request\UnfollowByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class FollowDomain extends BaseDomain {

    public UserDomain $user;
    public string $targetUserId;
    public FollowUser|null $followUser;

    public function __construct(
        UserDomain $user,
        string $targetUserId,
        FollowUser|null $followUser = null,
    ) {
        $this->user = $user;
        $this->targetUserId = $targetUserId;
        $this->followUser = $followUser;
    }

    public function follow(): FollowUser
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2FriendRestClient(
                    $session,
                );
                $result = $client->followByUserId(
                    (new FollowByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withTargetUserId($this->targetUserId)
                );
                return $result->getItem();
            }
        );
    }

    public function unfollow(): FollowUser
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2FriendRestClient(
                    $session,
                );
                $result = $client->unfollowByUserId(
                    (new UnfollowByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withTargetUserId($this->targetUserId)
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
                    $result = $client->getFollowByUserId(
                        (new GetFollowByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withTargetUserId($this->targetUserId)
                            ->withWithProfile(true)
                    );
                    return $result->getItem();
                }
            );
            $follow = new FollowDomain(
                $this->user,
                $this->targetUserId,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $follow = $this;
        }

        return view($view)
            ->with('follower', $follow);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'follower')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:friend:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:follower:{$this->targetUserId}")
                            ->orWhere('grn', 'like', "grn:friend:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:follower:{$this->targetUserId}:%")
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
            ->with("follow", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'follow_timeline'));
    }


}
