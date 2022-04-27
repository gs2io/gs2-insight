<?php

namespace App\Domain\Gs2Inventory;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

class ItemModelDomain extends BaseDomain {

    public InventoryModelDomain $inventoryModel;
    public string $itemModelName;

    public function __construct(
        InventoryModelDomain $inventoryModel,
        string $itemModelName,
    ) {
        $this->inventoryModel = $inventoryModel;
        $this->itemModelName = $itemModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("itemModel", $this);
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
                'namespaceName' => $this->inventoryModel->namespace->namespaceName,
                'inventoryName' => $this->inventoryModel->inventoryModelName,
                'itemName' => $this->itemModelName,
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
                'namespaceName' => $this->inventoryModel->namespace->namespaceName,
                'inventoryName' => $this->inventoryModel->inventoryModelName,
                'itemName' => $this->itemModelName,
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
                'namespaceName' => $this->inventoryModel->namespace->namespaceName,
                'inventoryName' => $this->inventoryModel->inventoryModelName,
                'itemName' => $this->itemModelName,
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
                'namespaceName' => $this->inventoryModel->namespace->namespaceName,
                'inventoryName' => $this->inventoryModel->inventoryModelName,
                'itemName' => $this->itemModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("itemModel", $this)
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
