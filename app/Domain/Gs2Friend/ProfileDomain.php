<?php

namespace App\Domain\Gs2Friend;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Friend\Gs2FriendRestClient;
use Gs2\Friend\Model\FriendUser;
use Gs2\Friend\Request\DeleteFriendByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ProfileDomain extends BaseDomain {

    public UserDomain $user;

    public function __construct(
        UserDomain $user,
    ) {
        $this->user = $user;
    }

    public function infoView(
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
