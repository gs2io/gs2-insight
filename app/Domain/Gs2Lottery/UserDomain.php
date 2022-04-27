<?php

namespace App\Domain\Gs2Lottery;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
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

    #[Pure] public function lottery(
        string $type,
    ): LotteryDomain {
        return new LotteryDomain(
            $this,
            $type
        );
    }

    public function lotteries(
        string $type = null,
    ): Builder {
        $types = Grn::query()
            ->where("parent", "grn:lottery:namespace:{$this->namespace->namespaceName}")
            ->where("category", "lotteryModel");
        if (!is_null($type)) {
            $types->where('key', '=', $type);
        }
        return $types;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

    public function lotteriesView(
        string $view,
        string $type = null,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("lotteries", (
            tap(
                $this->lotteries(
                    $type
                )->simplePaginate(10, ['*'], 'user_lotteries')
            )->transform(
                function ($grn) {
                    return new LotteryDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'lotteryModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:lottery:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:lottery:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function lotteryControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
