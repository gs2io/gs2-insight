<?php

namespace App\Domain\Gs2Inbox;

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

    public function sendMessageMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'inbox';
        $method = 'sendMessage';
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

    public function readMessageMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'inbox';
        $method = 'readMessage';
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

    public function deleteMessageMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'inbox';
        $method = 'deleteMessage';
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
                    $this->sendMessageMetricsView(
                        "hourly",
                    ),
                    $this->readMessageMetricsView(
                        "hourly",
                    ),
                    $this->deleteMessageMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->sendMessageMetricsView(
                        "daily",
                    ),
                    $this->readMessageMetricsView(
                        "daily",
                    ),
                    $this->deleteMessageMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->sendMessageMetricsView(
                        "weekly",
                    ),
                    $this->readMessageMetricsView(
                        "weekly",
                    ),
                    $this->deleteMessageMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->sendMessageMetricsView(
                        "monthly",
                    ),
                    $this->readMessageMetricsView(
                        "monthly",
                    ),
                    $this->deleteMessageMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
