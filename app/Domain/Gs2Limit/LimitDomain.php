<?php

namespace App\Domain\Gs2Limit;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Limit\Gs2LimitRestClient;
use Gs2\Limit\Model\Counter;
use Gs2\Limit\Request\CountUpByUserIdRequest;
use Gs2\Limit\Request\DescribeCountersByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class LimitDomain extends BaseDomain {

    public UserDomain $user;
    public string $limitModelName;

    public function __construct(
        UserDomain $user,
        string     $limitModelName,
    ) {
        $this->user = $user;
        $this->limitModelName = $limitModelName;
    }

    #[Pure] public function counter(
        string $type,
    ): CounterDomain {
        return new CounterDomain(
            $this,
            $type
        );
    }

    public function counters(
        string $type = null,
    ): Builder {
        $types = Grn::query()
            ->where("parent", "grn:limit:namespace:{$this->user->namespace->namespaceName}:limitModel:{$this->limitModelName}")
            ->where("category", "counter");
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
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withLimitName($this->limitModelName)
                    );
                    return array_map(
                        function (Counter $item) {
                            return new CounterDomain(
                                $this,
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
            ->with('limit', $this);
    }

    public function countersView(
        string $view,
        string $type = null,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("counters", (
            tap(
                $this->counters(
                    $type
                )->simplePaginate(10, ['*'], 'user_counters')
            )->transform(
                function ($grn) {
                    return new CounterDomain(
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
                            ->where('grn', '=', "grn:limit:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:limitModel:{$this->limitModelName}")
                            ->orWhere('grn', 'like', "grn:limit:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:limitModel:{$this->limitModelName}:%")
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
            ->with('user', $this->user);
    }

}
