<?php

namespace App\Domain\Gs2Inventory;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class NamespaceDomain extends BaseDomain {

    public string $namespaceName;

    public function __construct(
        string $namespaceName,
    ) {
        $this->namespaceName = $namespaceName;
    }

    #[Pure] public function inventoryModel(
        string $inventoryModelName,
    ): InventoryModelDomain {
        return new InventoryModelDomain(
            $this,
            $inventoryModelName
        );
    }

    public function inventoryModels(
        string $inventoryModelName = null,
    ): Builder {
        $inventoryModels = Grn::query()
            ->where("parent", "grn:inventory:namespace:{$this->namespaceName}")
            ->where("category", "inventoryModel");
        if (!is_null($inventoryModelName)) {
            $inventoryModels->where('key', 'like', "$inventoryModelName%");
        }
        return $inventoryModels;
    }

    #[Pure] public function user(
        string $userId,
    ): UserDomain {
        return new UserDomain(
            $this,
            $userId,
        );
    }

    public function users(
        string $userId = null,
    ): Builder {
        $users = Player::query();
        if (!is_null($userId)) {
            $users->where('userId', 'like', "$userId%");
        }
        return $users;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("namespace", $this);
    }

    public function inventoryModelsView(
        string $view,
        string $inventoryModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("inventoryModels", (
            tap(
                $this->inventoryModels(
                    $inventoryModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_inventoryModels')
            )->transform(
                function ($grn) {
                    return new InventoryModelDomain(
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
                'namespaceName' => $this->namespaceName,
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
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->acquireItemMetricsView(
                        "hourly",
                    ),
                    $this->consumeItemMetricsView(
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
                ],
                "weekly" => [
                    $this->acquireItemMetricsView(
                        "weekly",
                    ),
                    $this->consumeItemMetricsView(
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
                ],
            ]);
    }
}
