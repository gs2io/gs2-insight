<?php

namespace App\Domain\Gs2Inventory;

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

    #[Pure] public function inventory(
        string $itemName,
    ): InventoryDomain {
        return new InventoryDomain(
            $this,
            $itemName,
        );
    }

    public function inventories(
        string $itemName = null,
    ): Builder {
        $entries = Grn::query()
            ->where("parent", "grn:inventory:namespace:{$this->namespace->namespaceName}")
            ->where("category", "inventoryModel");
        if (!is_null($itemName)) {
            $entries->where('key', 'like', "$itemName%");
        }
        return $entries;
    }

    public function currentInventories(
    ): array {
        return $this->inventories()->get()->transform(
            function ($grn) {
                return new InventoryDomain(
                    $this,
                    $grn->key,
                );
            }
        )->toArray();
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("inventory", $this);
    }

    public function inventoriesView(
        string $view,
        string $propertyId = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("inventories", (
            tap(
                $this->inventories(
                    $propertyId,
                )
                    ->simplePaginate(10, ['*'], 'user_inventories')
            )->transform(
                function ($grn) {
                    return new InventoryDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentInventoriesView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("inventories", $this->currentInventories());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('grn', 'like', "grn:inventory:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%"),
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

    public function inventoryControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
