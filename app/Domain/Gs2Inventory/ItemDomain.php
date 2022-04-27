<?php

namespace App\Domain\Gs2Inventory;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Inventory\Gs2InventoryRestClient;
use Gs2\Inventory\Model\ItemSet;
use Gs2\Inventory\Request\AcquireItemSetByUserIdRequest;
use Gs2\Inventory\Request\ConsumeItemSetByUserIdRequest;
use Gs2\Inventory\Request\DescribeItemSetsByUserIdRequest;
use Gs2\Inventory\Request\GetItemSetByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ItemDomain extends BaseDomain {

    public InventoryDomain $inventory;
    public string $itemModelName;
    public array|null $itemSets;

    public function __construct(
        InventoryDomain $inventory,
        string          $itemModelName,
        array|null      $itemSets = null,
    ) {
        $this->inventory = $inventory;
        $this->itemModelName = $itemModelName;
        $this->itemSets = $itemSets;
    }

    public function acquire(
        int $count,
    )
    {
        return $this->gs2(
            function (Gs2RestSession $session) use ($count) {
                $client = new Gs2InventoryRestClient(
                    $session,
                );
                $client->acquireItemSetByUserId(
                    (new AcquireItemSetByUserIdRequest())
                        ->withNamespaceName($this->inventory->user->namespace->namespaceName)
                        ->withUserId($this->inventory->user->userId)
                        ->withInventoryName($this->inventory->inventoryModelName)
                        ->withItemName($this->itemModelName)
                        ->withAcquireCount($count)
                );
            }
        );
    }

    public function consume(
        int $count,
    )
    {
        return $this->gs2(
            function (Gs2RestSession $session) use ($count) {
                $client = new Gs2InventoryRestClient(
                    $session,
                );
                $client->consumeItemSetByUserId(
                    (new ConsumeItemSetByUserIdRequest())
                        ->withNamespaceName($this->inventory->user->namespace->namespaceName)
                        ->withUserId($this->inventory->user->userId)
                        ->withInventoryName($this->inventory->inventoryModelName)
                        ->withItemName($this->itemModelName)
                        ->withConsumeCount($count)
                );
            }
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $items = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2InventoryRestClient(
                        $session,
                    );
                    $result = $client->getItemSetByUserId(
                        (new GetItemSetByUserIdRequest())
                            ->withNamespaceName($this->inventory->user->namespace->namespaceName)
                            ->withUserId($this->inventory->user->userId)
                            ->withInventoryName($this->inventory->inventoryModelName)
                            ->withItemName($this->itemModelName)
                    );
                    return $result->getItems();
                }
            );

            $filteredItems = array_filter($items, function (ItemSet $item) {
                return $item->getItemName() == $this->itemModelName;
            });

            $item = new ItemDomain(
                $this->inventory,
                $this->itemModelName,
                $filteredItems,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $item = $this;
        }

        return view($view)
            ->with("item", $item);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'itemModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where("grn", "grn:inventory:namespace:{$this->inventory->user->namespace->namespaceName}:user:{$this->inventory->user->userId}:inventoryModel:{$this->inventory->inventoryModelName}:itemModel:{$this->itemModelName}")
                            ->orWhere("grn", "like", "grn:inventory:namespace:{$this->inventory->user->namespace->namespaceName}:user:{$this->inventory->user->userId}:inventoryModel:{$this->inventory->inventoryModelName}:itemModel:{$this->itemModelName}:%")
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
            ->with("item", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

}
