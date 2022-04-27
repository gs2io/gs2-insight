<?php

namespace App\Domain\Gs2Exchange;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Exchange\Gs2ExchangeRestClient;
use Gs2\Exchange\Model\Await;
use Gs2\Exchange\Request\DescribeAwaitsByUserIdRequest;
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

    #[Pure] public function rate(
        string $rateName,
    ): RateDomain {
        return new RateDomain(
            $this,
            $rateName
        );
    }

    public function rates(
        string $rateName = null,
    ): Builder {
        $rates = Grn::query()
            ->where("parent", "grn:exchange:namespace:{$this->namespace->namespaceName}")
            ->where("category", "rateModel");
        if (!is_null($rateName)) {
            $rates->where('key', 'like', "$rateName%");
        }
        return $rates;
    }

    public function currentAwaits(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2ExchangeRestClient(
                        $session,
                    );
                    $result = $client->describeAwaitsByUserId(
                        (new DescribeAwaitsByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                    );
                    return array_map(
                        function (Await $item) {
                            return new AwaitDomain(
                                new RateDomain(
                                    $this,
                                    $item->getRateName(),
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

    public function ratesView(
        string $view,
        string $rateName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("rates", (
            tap(
                $this->rates(
                    $rateName,
                )
                    ->simplePaginate(10, ['*'], 'user_rates')
            )->transform(
                function ($grn) {
                    return new RateDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentAwaitsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("awaits", $this->currentAwaits());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'rateModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:exchange:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:exchange:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function rateControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
