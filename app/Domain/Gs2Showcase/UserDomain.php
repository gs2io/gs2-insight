<?php

namespace App\Domain\Gs2Showcase;

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

    #[Pure] public function showcase(
        string $showcaseModelName,
    ): ShowcaseDomain {
        return new ShowcaseDomain(
            $this,
            $showcaseModelName
        );
    }

    public function showcases(
        string $showcaseModelName = null,
    ): Builder {
        $showcases = Grn::query()
            ->where("parent", "grn:showcase:namespace:{$this->namespace->namespaceName}")
            ->where("category", "showcaseModel");
        if (!is_null($showcaseModelName)) {
            $showcases->where('key', 'like', "$showcaseModelName%");
        }
        return $showcases;
    }

    public function showcasesView(
        string $view,
        string $showcaseModelName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("showcases", (
            tap(
                $this->showcases(
                    $showcaseModelName,
                )
                    ->simplePaginate(10, ['*'], 'user_showcases')
            )->transform(
                function ($grn) {
                    return new ShowcaseDomain(
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
                    ->where('category', 'showcaseModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:showcase:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:showcase:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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

    public function showcaseControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
