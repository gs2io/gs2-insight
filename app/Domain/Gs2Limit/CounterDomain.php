<?php

namespace App\Domain\Gs2Limit;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Limit\Gs2LimitRestClient;
use Gs2\Limit\Model\Counter;
use Gs2\Limit\Request\CountUpByUserIdRequest;
use Gs2\Limit\Request\DeleteCounterByUserIdRequest;
use Gs2\Limit\Request\GetCounterByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class CounterDomain extends BaseDomain {

    public LimitDomain $limit;
    public string $counterModelName;
    public Counter|null $counter;

    public function __construct(
        LimitDomain $limit,
        string     $counterModelName,
        Counter|null $counter = null,
    ) {
        $this->limit = $limit;
        $this->counterModelName = $counterModelName;
        $this->counter = $counter;
    }

    public function increase(
        int $increaseValue,
    ) {
        $this->gs2(
            function (Gs2RestSession $session) use ($increaseValue) {
                $client = new Gs2LimitRestClient(
                    $session,
                );
                $client->countUpByUserId(
                    (new CountUpByUserIdRequest())
                        ->withNamespaceName($this->limit->user->namespace->namespaceName)
                        ->withUserId($this->limit->user->userId)
                        ->withLimitName($this->limit->limitModelName)
                        ->withCounterName($this->counterModelName)
                        ->withCountUpValue($increaseValue)
                        ->withMaxValue(2147483646)
                );
                return null;
            }
        );
    }

    public function reset() {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2LimitRestClient(
                    $session,
                );
                $client->deleteCounterByUserId(
                    (new DeleteCounterByUserIdRequest())
                        ->withNamespaceName($this->limit->user->namespace->namespaceName)
                        ->withUserId($this->limit->user->userId)
                        ->withLimitName($this->limit->limitModelName)
                        ->withCounterName($this->counterModelName)
                );
                return null;
            }
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2LimitRestClient(
                        $session,
                    );
                    $result = $client->getCounterByUserId(
                        (new GetCounterByUserIdRequest())
                            ->withNamespaceName($this->limit->user->namespace->namespaceName)
                            ->withUserId($this->limit->user->userId)
                            ->withLimitName($this->limit->limitModelName)
                            ->withCounterName($this->counterModelName)
                    );
                    return $result->getItem();
                }
            );
            $counter = new CounterDomain(
                $this->limit,
                $this->counterModelName,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $counter = $this;
        }

        return view($view)
            ->with("counter", $counter);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'counter')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:limit:namespace:{$this->limit->user->namespace->namespaceName}:user:{$this->limit->user->userId}:limitModel:{$this->limit->limitModelName}:counter:{$this->counterModelName}")
                            ->orWhere('grn', 'like', "grn:limit:namespace:{$this->limit->user->namespace->namespaceName}:user:{$this->limit->user->userId}:limitModel:{$this->limit->limitModelName}:counter:{$this->counterModelName}:%")
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
            ->with("counter", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

}
