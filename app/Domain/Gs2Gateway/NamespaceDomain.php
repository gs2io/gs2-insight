<?php

namespace App\Domain\Gs2Gateway;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\Player;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Gateway\Gs2GatewayRestClient;
use Gs2\Gateway\Request\DisconnectAllRequest;
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

    public function disconnectAll() {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2GatewayRestClient(
                    $session,
                );
                $client->disconnectAll(
                    (new DisconnectAllRequest())
                        ->withNamespaceName($this->namespaceName)
                );
            }
        );
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

    public function downloadMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'gateway';
        $method = 'download';
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

    public function uploadMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'gateway';
        $method = 'upload';
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
                    $this->downloadMetricsView(
                        "hourly",
                    ),
                    $this->uploadMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->downloadMetricsView(
                        "daily",
                    ),
                    $this->uploadMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->downloadMetricsView(
                        "weekly",
                    ),
                    $this->uploadMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->downloadMetricsView(
                        "monthly",
                    ),
                    $this->uploadMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
