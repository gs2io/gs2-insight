<?php

namespace App\Domain\Gs2Account;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Account\Gs2AccountRestClient;
use Gs2\Account\Model\TakeOver;
use Gs2\Account\Request\CreateTakeOverByUserIdRequest;
use Gs2\Account\Request\DeleteTakeOverByUserIdentifierRequest;
use Gs2\Account\Request\DescribeTakeOversByUserIdRequest;
use Gs2\Core\Net\Gs2RestSession;
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

    #[Pure] public function takeOver(
        string $type,
    ): TakeOverDomain {
        return new TakeOverDomain(
            $this,
            $type
        );
    }

    public function takeOvers(
        string $type = null,
    ): Builder {
        $types = Grn::query()
            ->where("parent", "grn:account:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
            ->where("category", "takeOver");
        if (!is_null($type)) {
            $types->where('key', '=', $type);
        }
        return $types;
    }

    public function currentTakeOvers(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2AccountRestClient(
                        $session,
                    );
                    $result = $client->describeTakeOversByUserId(
                        (new DescribeTakeOversByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                    );
                    return array_map(
                        function (TakeOver $item) {
                            return new TakeOverDomain(
                                $this,
                                $item->getType(),
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

    #[Pure] public function dataOwner(): DataOwnerDomain {
        return new DataOwnerDomain(
            $this
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

    public function takeOversView(
        string $view,
        string $type = null,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("takeOvers", (
                tap(
                    $this->takeOvers(
                        $type
                    )->simplePaginate(10, ['*'], 'user_takeOvers')
                )->transform(
                    function ($grn) {
                        return new TakeOverDomain(
                            $this,
                            $grn->key,
                        );
                    }
                )
            ));
    }

    public function currentTakeOversView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("takeOvers", $this->currentTakeOvers());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'takeOver')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:account:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:account:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function takeOverControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }
}
