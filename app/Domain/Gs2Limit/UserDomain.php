<?php

namespace App\Domain\Gs2Limit;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Limit\Gs2LimitRestClient;
use Gs2\Limit\Model\Counter;
use Gs2\Limit\Request\DescribeCountersByUserIdRequest;
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

    #[Pure] public function limit(
        string $type,
    ): LimitDomain {
        return new LimitDomain(
            $this,
            $type
        );
    }

    public function limits(
        string $type = null,
    ): Builder {
        $types = Grn::query()
            ->where("parent", "grn:limit:namespace:{$this->namespace->namespaceName}")
            ->where("category", "limitModel");
        if (!is_null($type)) {
            $types->where('key', '=', $type);
        }
        return $types;
    }

    public function currentCounters(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2LimitRestClient(
                        $session,
                    );
                    $result = $client->describeCountersByUserId(
                        (new DescribeCountersByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                    );
                    return array_map(
                        function (Counter $item) {
                            return new CounterDomain(
                                new LimitDomain(
                                    $this,
                                    $item->getLimitName(),
                                ),
                                $item->getName(),
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

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

    public function limitsView(
        string $view,
        string $type = null,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("limits", (
            tap(
                $this->limits(
                    $type
                )->simplePaginate(10, ['*'], 'user_limits')
            )->transform(
                function ($grn) {
                    return new LimitDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentCountersView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("counters", $this->currentCounters());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'limitModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:limit:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:limit:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function counterControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
