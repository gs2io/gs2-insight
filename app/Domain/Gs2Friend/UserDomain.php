<?php

namespace App\Domain\Gs2Friend;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Friend\Gs2FriendRestClient;
use Gs2\Friend\Model\FollowUser;
use Gs2\Friend\Model\Friend;
use Gs2\Friend\Model\FriendRequest;
use Gs2\Friend\Model\FriendUser;
use Gs2\Friend\Request\DescribeFollowsByUserIdRequest;
use Gs2\Friend\Request\DescribeFriendsByUserIdRequest;
use Gs2\Friend\Request\DescribeReceiveRequestsByUserIdRequest;
use Gs2\Friend\Request\DescribeSendRequestsByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class UserDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $userId;

    public function __construct(
        NamespaceDomain $namespace,
        string $userId,
    ) {
        $this->namespace = $namespace;
        $this->userId = $userId;
    }

    #[Pure] public function friend(
        string $friendName,
    ): FriendDomain {
        return new FriendDomain(
            $this,
            $friendName
        );
    }

    public function friends(
        string $friendName = null,
    ): Builder {
        $friends = Grn::query()
            ->where("parent", "grn:friend:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "friend");
        if (!is_null($friendName)) {
            $friends->where('key', 'like', "$friendName%");
        }
        return $friends;
    }

    public function friendsView(
        string $view,
        string $friendName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("friends", (
            tap(
                $this->friends(
                    $friendName,
                )
                    ->simplePaginate(10, ['*'], 'user_friends')
            )->transform(
                function ($grn) {
                    return new FriendDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    #[Pure] public function follower(
        string $followerName,
    ): FollowDomain {
        return new FollowDomain(
            $this,
            $followerName
        );
    }

    public function followers(
        string $followerName = null,
    ): Builder {
        $followers = Grn::query()
            ->where("parent", "grn:friend:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "follower");
        if (!is_null($followerName)) {
            $followers->where('key', 'like', "$followerName%");
        }
        return $followers;
    }

    #[Pure] public function sendRequest(
        string $targetUserId,
    ): SendRequestDomain {
        return new SendRequestDomain(
            $this,
            $targetUserId
        );
    }

    #[Pure] public function receiveRequest(
        string $targetUserId,
    ): ReceiveRequestDomain {
        return new ReceiveRequestDomain(
            $this,
            $targetUserId
        );
    }

    #[Pure] public function profile(): ProfileDomain {
        return new ProfileDomain(
            $this,
        );
    }

    public function currentFriends(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2FriendRestClient(
                        $session,
                    );
                    $result = $client->describeFriendsByUserId(
                        (new DescribeFriendsByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                    );
                    return array_map(
                        function (FriendUser $item) {
                            return new FriendDomain(
                                $this,
                                $item->getUserId(),
                                $item,
                            );
                        }
                        , $result->getItems());
                }
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return [];
    }

    public function currentFollowers(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2FriendRestClient(
                        $session,
                    );
                    $result = $client->describeFollowsByUserId(
                        (new DescribeFollowsByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                    );
                    return array_map(
                        function (FollowUser $item) {
                            return new FollowDomain(
                                $this,
                                $item->getUserId(),
                                $item,
                            );
                        }
                        , $result->getItems());
                }
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return [];
    }

    public function currentReceiveRequests(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2FriendRestClient(
                        $session,
                    );
                    $result = $client->describeReceiveRequestsByUserId(
                        (new DescribeReceiveRequestsByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                    );
                    return array_map(
                        function (FriendRequest $item) {
                            return new ReceiveRequestDomain(
                                $this,
                                $item->getUserId(),
                                $item,
                            );
                        }
                        , $result->getItems());
                }
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return [];
    }

    public function currentSendRequests(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2FriendRestClient(
                        $session,
                    );
                    $result = $client->describeSendRequestsByUserId(
                        (new DescribeSendRequestsByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                    );
                    return array_map(
                        function (FriendRequest $item) {
                            return new SendRequestDomain(
                                $this,
                                $item->getUserId(),
                                $item,
                            );
                        }
                        , $result->getItems());
                }
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return [];
    }

    public function followersView(
        string $view,
        string $followerName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("followers", (
            tap(
                $this->followers(
                    $followerName,
                )
                    ->simplePaginate(10, ['*'], 'user_followers')
            )->transform(
                function ($grn) {
                    return new FollowDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentFriendsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("friends", $this->currentFriends());
    }

    public function currentFollowersView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("followers", $this->currentFollowers());
    }

    public function currentReceiveRequestsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("receiveRequests", $this->currentReceiveRequests());
    }

    public function currentSendRequestsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("sendRequests", $this->currentSendRequests());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->whereIn('category', ['friend', 'follower', 'profile'])
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:friend:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:friend:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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
            ->with("user", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

    public function messageControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

    public function friendControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

    public function followerControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

    public function sendRequestControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

    public function receiveRequestControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
