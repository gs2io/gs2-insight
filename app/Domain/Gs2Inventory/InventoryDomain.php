<?php

namespace App\Domain\Gs2Inventory;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Inventory\Gs2InventoryRestClient;
use Gs2\Inventory\Model\Inventory;
use Gs2\Inventory\Model\ItemSet;
use Gs2\Inventory\Request\DescribeItemSetsByUserIdRequest;
use Gs2\Inventory\Request\GetInventoryByUserIdRequest;
use Gs2\Inventory\Request\SetCapacityByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class InventoryDomain extends BaseDomain {

    public UserDomain $user;
    public string $inventoryModelName;
    public Inventory|null $inventory;

    public function __construct(
        UserDomain $user,
        string     $inventoryModelName,
        Inventory|null $inventory = null,
    ) {
        $this->user = $user;
        $this->inventoryModelName = $inventoryModelName;
        $this->inventory = $inventory;
    }

    public function updateCapacity(
        int $capacity,
    )
    {
        return $this->gs2(
            function (Gs2RestSession $session) use ($capacity) {
                $client = new Gs2InventoryRestClient(
                    $session,
                );
                $client->setCapacityByUserId(
                    (new SetCapacityByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withInventoryName($this->inventoryModelName)
                        ->withNewCapacityValue($capacity)
                );
            }
        );
    }

    #[Pure] public function item(
        string $itemName,
    ): ItemDomain {
        return new ItemDomain(
            $this,
            $itemName,
        );
    }

    public function items(
        string $itemName = null,
    ): Builder {
        $entries = Grn::query()
            ->where('category', 'itemModel')
            ->addNestedWhereQuery(
                GrnKey::query()
                    ->where("parent", "grn:inventory:namespace:{$this->user->namespace->namespaceName}:inventoryModel:{$this->inventoryModelName}")
                    ->orWhere("parent", "like", "grn:inventory:namespace:{$this->user->namespace->namespaceName}:inventoryModel:{$this->inventoryModelName}:%")
                    ->getQuery()
            );
        if (!is_null($itemName)) {
            $entries->where('key', 'like', "$itemName%");
        }
        return $entries;
    }

    public function currentItems(
    ): array {
        try {
            $items = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2InventoryRestClient(
                        $session,
                    );
                    $result = $client->describeItemSetsByUserId(
                        (new DescribeItemSetsByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withInventoryName($this->inventoryModelName)
                    );
                    return $result->getItems();
                }
            );

            return $this->items()->get()->transform(
                function ($grn) use ($items) {

                    $filteredItems = array_filter($items, function (ItemSet $item) use ($grn) {
                        return $item->getItemName() == $grn->key;
                    });

                    return new ItemDomain(
                        $this,
                        $grn->key,
                        $filteredItems,
                    );
                }
            )->toArray();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            return [];
        }
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2InventoryRestClient(
                        $session,
                    );
                    $result = $client->getInventoryByUserId(
                        (new GetInventoryByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withInventoryName($this->inventoryModelName)
                    );
                    return $result->getItem();
                }
            );

            $inventory = new InventoryDomain(
                $this->user,
                $this->inventoryModelName,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $inventory = $this;
        }

        return view($view)
            ->with("inventory", $inventory);
    }

    public function itemsView(
        string $view,
        string $propertyId = null,
    ): View
    {
        return view($view)
            ->with("inventory", $this)
            ->with("items", (
            tap(
                $this->items(
                    $propertyId,
                )
                    ->simplePaginate(10, ['*'], 'user_itemSets')
            )->transform(
                function ($grn) {
                    return new ItemDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentItemsView(
        string $view,
    ): View
    {
        return view($view)
            ->with('inventory', $this)
            ->with("items", $this->currentItems());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'itemModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:inventory:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:inventoryModel:{$this->inventoryModelName}")
                            ->orWhere('grn', 'like', "grn:inventory:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:inventoryModel:{$this->inventoryModelName}:%")
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
            ->with("inventory", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

    public function itemControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with("inventory", $this);
    }

    public function controllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with("inventory", $this);
    }
}
