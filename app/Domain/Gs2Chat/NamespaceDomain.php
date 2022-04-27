<?php

namespace App\Domain\Gs2Chat;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class NamespaceDomain extends BaseDomain {

    public string $namespaceName;

    public function __construct(
        string $namespaceName,
    ) {
        $this->namespaceName = $namespaceName;
    }

    #[Pure] public function room(
        string $roomName,
    ): RoomDomain {
        return new RoomDomain(
            $this,
            $roomName
        );
    }

    public function rooms(
        string $roomName = null,
    ): Builder {
        $rooms = Grn::query()
            ->where("parent", "grn:chat:namespace:{$this->namespaceName}")
            ->where("category", "room");
        if (!is_null($roomName)) {
            $rooms->where('key', 'like', "$roomName%");
        }
        return $rooms;
    }

    #[Pure] public function user(
        string $userId,
    ): UserDomain {
        return new UserDomain(
            $this,
            $userId,
        );
    }

    public function users(
        string $userId = null,
    ): Builder {
        $users = Player::query();
        if (!is_null($userId)) {
            $users->where('userId', 'like', "$userId%");
        }
        return $users;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("namespace", $this);
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
                        return new RoomDomain(
                            $this,
                            $grn->key,
                        );
                    }
                )
            ));
    }

    public function createRoomMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'chat';
        $method = 'createRoom';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function describeMessagesMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'chat';
        $method = 'describeMessages';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function postMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'chat';
        $method = 'post';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function subscribeMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'chat';
        $method = 'subscribe';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function unsubscribeMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'chat';
        $method = 'unsubscribe';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->createRoomMetricsView(
                        "hourly",
                    ),
                    $this->describeMessagesMetricsView(
                        "hourly",
                    ),
                    $this->postMetricsView(
                        "hourly",
                    ),
                    $this->subscribeMetricsView(
                        "hourly",
                    ),
                    $this->unsubscribeMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->createRoomMetricsView(
                        "daily",
                    ),
                    $this->describeMessagesMetricsView(
                        "daily",
                    ),
                    $this->postMetricsView(
                        "daily",
                    ),
                    $this->subscribeMetricsView(
                        "daily",
                    ),
                    $this->unsubscribeMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->createRoomMetricsView(
                        "weekly",
                    ),
                    $this->describeMessagesMetricsView(
                        "weekly",
                    ),
                    $this->postMetricsView(
                        "weekly",
                    ),
                    $this->subscribeMetricsView(
                        "weekly",
                    ),
                    $this->unsubscribeMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->createRoomMetricsView(
                        "monthly",
                    ),
                    $this->describeMessagesMetricsView(
                        "monthly",
                    ),
                    $this->postMetricsView(
                        "monthly",
                    ),
                    $this->subscribeMetricsView(
                        "monthly",
                    ),
                    $this->unsubscribeMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
