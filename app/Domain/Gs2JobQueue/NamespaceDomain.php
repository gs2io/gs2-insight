<?php

namespace App\Domain\Gs2JobQueue;

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

    public function pushMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'jobQueue';
        $method = 'push';
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

    public function runMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'jobQueue';
        $method = 'run';
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
                    $this->pushMetricsView(
                        "hourly",
                    ),
                    $this->runMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->pushMetricsView(
                        "daily",
                    ),
                    $this->runMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->pushMetricsView(
                        "weekly",
                    ),
                    $this->runMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->pushMetricsView(
                        "monthly",
                    ),
                    $this->runMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
