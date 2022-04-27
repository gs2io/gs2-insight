<?php

namespace App\Domain\Gs2Friend;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Friend\Gs2FriendRestClient;
use Gs2\Friend\Model\FriendUser;
use Gs2\Friend\Request\DeleteFriendByUserIdRequest;
use Gs2\Friend\Request\GetFriendByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class FriendDomain extends BaseDomain {

    public UserDomain $user;
    public string $targetUserId;
    public FriendUser|null $friendUser;

    public function __construct(
        UserDomain $user,
        string $targetUserId,
        FriendUser|null $friendUser = null,
    ) {
        $this->user = $user;
        $this->targetUserId = $targetUserId;
        $this->friendUser = $friendUser;
    }

    public function delete(): FriendUser
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2FriendRestClient(
                    $session,
                );
                $result = $client->deleteFriendByUserId(
                    (new DeleteFriendByUserIdRequest())
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
                    $result = $client->getFriendByUserId(
                        (new GetFriendByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withTargetUserId($this->targetUserId)
                            ->withWithProfile(true)
                    );
                    return $result->getItem();
                }
            );
            $friend = new FriendDomain(
                $this->user,
                $this->targetUserId,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $friend = $this;
        }

        return view($view)
            ->with('friend', $friend);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'friend')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:friend:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:friend:{$this->targetUserId}")
                            ->orWhere('grn', 'like', "grn:friend:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:friend:{$this->targetUserId}:%")
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
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'friend_timeline'));
    }

}
