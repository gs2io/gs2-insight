<?php

namespace App\Domain\Gs2Realtime;

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

    public function wantRoomMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'realtime';
        $method = 'wantRoom';
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
                    $this->wantRoomMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->wantRoomMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->wantRoomMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->wantRoomMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
