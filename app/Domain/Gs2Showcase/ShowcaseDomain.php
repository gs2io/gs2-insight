<?php

namespace App\Domain\Gs2Showcase;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class ShowcaseDomain extends BaseDomain {

    public UserDomain $user;
    public string $showcaseModelName;

    public function __construct(
        UserDomain $user,
        string     $showcaseModelName,
    ) {
        $this->user = $user;
        $this->showcaseModelName = $showcaseModelName;
    }

    #[Pure] public function displayItem(
        string $displayItemId,
    ): DisplayItemDomain {
        return new DisplayItemDomain(
            $this,
            $displayItemId
        );
    }

    public function displayItems(
        string $displayItemId = null,
    ): Builder {
        $displayItems = Grn::query()
            ->where("parent", "grn:showcase:namespace:{$this->user->namespace->namespaceName}:showcaseModel:{$this->showcaseModelName}")
            ->where("category", "displayItemModel");
        if (!is_null($displayItemId)) {
            $displayItems->where('key', 'like', "$displayItemId%");
        }
        return $displayItems;
    }

    public function displayItemsView(
        string $view,
        string $displayItemId = null,
    ): View
    {
        return view($view)
            ->with("showcase", $this)
            ->with("displayItems", (
            tap(
                $this->displayItems(
                    $displayItemId,
                )
                    ->simplePaginate(10, ['*'], 'user_displayItems')
            )->transform(
                function ($grn) {
                    return new DisplayItemDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("showcase", $this);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'displayItemModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:showcase:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:showcaseModel:{$this->showcaseModelName}")
                            ->orWhere('grn', 'like', "grn:showcase:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:showcaseModel:{$this->showcaseModelName}:%")
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
            ->with("showcase", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

    public function displayItemControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('showcase', $this);
    }

}
