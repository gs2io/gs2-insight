<?php

namespace App\Domain\Gs2Mission;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Mission\Gs2MissionRestClient;
use Gs2\Mission\Model\Counter;
use Gs2\Mission\Request\DeleteCounterByUserIdRequest;
use Gs2\Mission\Request\GetCounterByUserIdRequest;
use Gs2\Mission\Request\IncreaseCounterByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class CounterDomain extends BaseDomain {

    public UserDomain $user;
    public string $counterModelName;
    public Counter|null $counter;

    public function __construct(
        UserDomain $user,
        string     $counterModelName,
        Counter|null $counter = null,
    ) {
        $this->user = $user;
        $this->counterModelName = $counterModelName;
        $this->counter = $counter;
    }

    public function increase(
        int $increaseValue,
    ) {
        $this->gs2(
            function (Gs2RestSession $session) use ($increaseValue) {
                $client = new Gs2MissionRestClient(
                    $session,
                );
                $client->increaseCounterByUserId(
                    (new IncreaseCounterByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withCounterName($this->counterModelName)
                        ->withValue($increaseValue)
                );
                return null;
            }
        );
    }

    public function reset() {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2MissionRestClient(
                    $session,
                );
                $client->deleteCounterByUserId(
                    (new DeleteCounterByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
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
                    $client = new Gs2MissionRestClient(
                        $session,
                    );
                    $result = $client->getCounterByUserId(
                        (new GetCounterByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withCounterName($this->counterModelName)
                    );
                    return $result->getItem();
                }
            );

            $counter = new CounterDomain(
                $this->user,
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
                    ->where('category', 'counterModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:mission:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:counterModel:{$this->counterModelName}")
                            ->orWhere('grn', 'like', "grn:mission:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:counterModel:{$this->counterModelName}:%")
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
