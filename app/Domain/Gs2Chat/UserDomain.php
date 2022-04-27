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
use Gs2\Core\Net\Gs2RestSession;
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

    #[Pure] public function subscribe(
        string $subscribeName,
    ): SubscribeDomain {
        return new SubscribeDomain(
            $this,
            $subscribeName
        );
    }

    public function subscribes(
        string $roomName = null,
    ): Builder {
        $subscribes = Grn::query()
            ->where("parent", "grn:chat:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "subscribe");
        if (!is_null($roomName)) {
            $subscribes->where('key', 'like', "$roomName%");
        }
        return $subscribes;
    }

    #[Pure] public function room(
        string $roomName,
    ): RoomByUserDomain {
        return new RoomByUserDomain(
            $this,
            $roomName
        );
    }

    public function rooms(
        string $roomName = null,
    ): Builder {
        $rooms = Grn::query()
            ->where("parent", "grn:chat:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "room");
        if (!is_null($roomName)) {
            $rooms->where('key', 'like', "$roomName%");
        }
        return $rooms;
    }

    public function currentSubscribes(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2ChatRestClient(
                        $session,
                    );
                    $result = $client->describeSubscribesByUserId(
                        (new DescribeSubscribesByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                            ->withLimit(1000)
                    );
                    return array_map(
                        function (Subscribe $item) {
                            return new SubscribeDomain(
                                $this,
                                $item->getRoomName(),
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

    public function roomsView(
        string $view,
        string $roomName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("rooms", (
            tap(
                $this->rooms(
                    $roomName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_rooms')
            )->transform(
                function ($grn) {
                    return new RoomByUserDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentSubscribesView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("subscribes", $this->currentSubscribes());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'room')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:chat:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:chat:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function subscribeControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
