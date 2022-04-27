<?php

namespace App\Domain\Gs2Inventory;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class InventoryModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $inventoryModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $inventoryModelName,
    ) {
        $this->namespace = $namespace;
        $this->inventoryModelName = $inventoryModelName;
    }

    #[Pure] public function itemModel(
        string $itemModelName,
    ): ItemModelDomain {
        return new ItemModelDomain(
            $this,
            $itemModelName
        );
    }

    public function itemModels(
        string $itemModelName = null,
    ): Builder {
        $itemModels = Grn::query()
            ->where("parent", "grn:inventory:namespace:{$this->namespace->namespaceName}:inventoryModel:{$this->inventoryModelName}")
            ->where("category", "itemModel");
        if (!is_null($itemModelName)) {
            $itemModels->where('key', 'like', "$itemModelName%");
        }
        return $itemModels;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("inventoryModel", $this);
    }

    public function itemModelsView(
        string $view,
        string $itemModelName = null,
    ): View
    {
        return view($view)
            ->with("inventoryModel", $this)
            ->with("itemModels", (
            tap(
                $this->itemModels(
                    $itemModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_itemModels')
            )->transform(
                function ($grn) {
                    return new ItemModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function acquireItemMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'inventory';
        $method = 'acquireItem';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'inventoryName' => $this->inventoryModelName,
            ]
        );
    }

    public function consumeItemMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'inventory';
        $method = 'consumeItem';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'inventoryName' => $this->inventoryModelName,
            ]
        );
    }

    public function acquireSumItemMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'inventory';
        $method = 'acquireItem';
        $category = 'sum:acquireCount';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'inventoryName' => $this->inventoryModelName,
            ]
        );
    }

    public function consumeSumItemMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'inventory';
        $method = 'consumeItem';
        $category = 'sun:consumeCount';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'inventoryName' => $this->inventoryModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("inventoryModel", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->acquireItemMetricsView(
                        "hourly",
                    ),
                    $this->consumeItemMetricsView(
                        "hourly",
                    ),
                    $this->acquireSumItemMetricsView(
                        "hourly",
                    ),
                    $this->consumeSumItemMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->acquireItemMetricsView(
                        "daily",
                    ),
                    $this->consumeItemMetricsView(
                        "daily",
                    ),
                    $this->acquireSumItemMetricsView(
                        "daily",
                    ),
                    $this->consumeSumItemMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->acquireItemMetricsView(
                        "weekly",
                    ),
                    $this->consumeItemMetricsView(
                        "weekly",
                    ),
                    $this->acquireSumItemMetricsView(
                        "weekly",
                    ),
                    $this->consumeSumItemMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->acquireItemMetricsView(
                        "monthly",
                    ),
                    $this->consumeItemMetricsView(
                        "monthly",
                    ),
                    $this->acquireSumItemMetricsView(
                        "monthly",
                    ),
                    $this->consumeSumItemMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
