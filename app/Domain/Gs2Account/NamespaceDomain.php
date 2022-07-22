<?php

namespace App\Domain\Gs2Account;

use App\Domain\BaseDomain;
use App\Models\Player;
use Gs2\Account\Gs2AccountRestClient;
use Gs2\Account\Model\Namespace_;
use Gs2\Account\Request\GetNamespaceRequest;
use Gs2\Core\Net\Gs2RestSession;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class NamespaceDomain extends BaseDomain {

    public string $namespaceName;
    public ?Namespace_ $namespace;

    public function __construct(
        string $namespaceName,
        Namespace_ $namespace = null,
    ) {
        $this->namespaceName = $namespaceName;
        $this->namespace = $namespace;
    }

    public function model(): NamespaceDomain
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2AccountRestClient(
                        $session,
                    );
                    $result = $client->getNamespace(
                        (new GetNamespaceRequest())
                            ->withNamespaceName($this->namespaceName)
                    );
                    return $result->getItem();
                }
            );
            return new NamespaceDomain(
                $this->namespaceName,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return $this;
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

    public function usersView(
        string $view,
        string $userId = null,
    ): View
    {
        return view($view)
            ->with('namespace', $this)
            ->with("users", (
                tap(
                    $this->users(
                        $userId,
                    )->simplePaginate(10, ['*'], 'service_users')
                )->transform(
                    function ($grn) {
                        return new UserDomain(
                            $this,
                            $grn->key,
                        );
                    }
                )
            ));
    }

    public function authenticationMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'account';
        $method = 'authentication';
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

    public function createAccountMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'account';
        $method = 'createAccount';
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

    public function createTakeOverMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'account';
        $method = 'createTakeOver';
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

    public function doTakeOverMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'account';
        $method = 'doTakeOver';
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
                    $this->createAccountMetricsView(
                        "hourly",
                    ),
                    $this->authenticationMetricsView(
                        "hourly",
                    ),
                    $this->createTakeOverMetricsView(
                        "hourly",
                    ),
                    $this->doTakeOverMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->createAccountMetricsView(
                        "daily",
                    ),
                    $this->authenticationMetricsView(
                        "daily",
                    ),
                    $this->createTakeOverMetricsView(
                        "daily",
                    ),
                    $this->doTakeOverMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->createAccountMetricsView(
                        "weekly",
                    ),
                    $this->authenticationMetricsView(
                        "weekly",
                    ),
                    $this->createTakeOverMetricsView(
                        "weekly",
                    ),
                    $this->doTakeOverMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->createAccountMetricsView(
                        "monthly",
                    ),
                    $this->authenticationMetricsView(
                        "monthly",
                    ),
                    $this->createTakeOverMetricsView(
                        "monthly",
                    ),
                    $this->doTakeOverMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
