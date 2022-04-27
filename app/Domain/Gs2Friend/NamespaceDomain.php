<?php

namespace App\Domain\Gs2Friend;

use App\Domain\BaseDomain;
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

    public function acceptRequestMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'friend';
        $method = 'acceptRequest';
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

    public function followMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'friend';
        $method = 'follow';
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

    public function rejectMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'friend';
        $method = 'reject';
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

    public function sendRequestMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'friend';
        $method = 'sendRequest';
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

    public function unfollowMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'friend';
        $method = 'unfollow';
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
                    $this->acceptRequestMetricsView(
                        "hourly",
                    ),
                    $this->followMetricsView(
                        "hourly",
                    ),
                    $this->rejectMetricsView(
                        "hourly",
                    ),
                    $this->sendRequestMetricsView(
                        "hourly",
                    ),
                    $this->unfollowMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->acceptRequestMetricsView(
                        "daily",
                    ),
                    $this->followMetricsView(
                        "daily",
                    ),
                    $this->rejectMetricsView(
                        "daily",
                    ),
                    $this->sendRequestMetricsView(
                        "daily",
                    ),
                    $this->unfollowMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->acceptRequestMetricsView(
                        "weekly",
                    ),
                    $this->followMetricsView(
                        "weekly",
                    ),
                    $this->rejectMetricsView(
                        "weekly",
                    ),
                    $this->sendRequestMetricsView(
                        "weekly",
                    ),
                    $this->unfollowMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->acceptRequestMetricsView(
                        "monthly",
                    ),
                    $this->followMetricsView(
                        "monthly",
                    ),
                    $this->rejectMetricsView(
                        "monthly",
                    ),
                    $this->sendRequestMetricsView(
                        "monthly",
                    ),
                    $this->unfollowMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
