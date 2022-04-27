<?php

namespace App\Domain\Gs2Chat;

use App\Domain\BaseDomain;
use App\Domain\PlayerDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Chat\Gs2ChatRestClient;
use Gs2\Chat\Model\Subscribe;
use Gs2\Chat\Request\DescribeSubscribesByUserIdRequest;
use Gs2\Chat\Request\GetSubscribeByUserIdRequest;
use Gs2\Core\Net\Gs2RestSession;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class RoomByUserDomain extends BaseDomain {

    public UserDomain $user;
    public string $roomName;

    public function __construct(
        UserDomain $user,
        string $roomName,
    ) {
        $this->user = $user;
        $this->roomName = $roomName;
    }

    #[Pure] public function message(
        string $messageName,
    ): MessageDomain {
        return new MessageDomain(
            $this,
            $messageName,
        );
    }

    public function messages(
        string $messageName = null,
    ): Builder {
        $messages = Grn::query()
            ->where("parent", "grn:chat:namespace:{$this->user->namespace->namespaceName}:room:{$this->roomName}")
            ->where("category", "message");
        if (!is_null($messageName)) {
            $messages->where('key', 'like', "$messageName%");
        }
        return $messages;
    }

    #[Pure] public function subscribe(
    ): SubscribeDomain {
        return new SubscribeDomain(
            $this->user,
            $this->roomName,
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("room", $this);
    }

    public function messagesView(
        string $view,
    ): View
    {
        return view($view)
            ->with("messages", $this->messages());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'room')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:chat:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:room:{$this->roomName}")
                            ->orWhere('grn', 'like', "grn:chat:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:room:{$this->roomName}:%")
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
}
