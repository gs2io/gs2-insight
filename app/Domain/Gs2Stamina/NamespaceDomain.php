<?php

namespace App\Domain\Gs2Stamina;

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

    #[Pure] public function staminaModel(
        string $staminaModelName,
    ): StaminaModelDomain {
        return new StaminaModelDomain(
            $this,
            $staminaModelName
        );
    }

    public function staminaModels(
        string $staminaModelName = null,
    ): Builder {
        $staminaModels = Grn::query()
            ->where("parent", "grn:stamina:namespace:{$this->namespaceName}")
            ->where("category", "staminaModel");
        if (!is_null($staminaModelName)) {
            $staminaModels->where('key', 'like', "$staminaModelName%");
        }
        return $staminaModels;
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

    public function staminaModelsView(
        string $view,
        string $staminaModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("staminaModels", (
            tap(
                $this->staminaModels(
                    $staminaModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_staminaModels')
            )->transform(
                function ($grn) {
                    return new StaminaModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function consumeStaminaMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'stamina';
        $method = 'consumeStamina';
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

    public function raiseMaxValueMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'stamina';
        $method = 'raiseMaxValue';
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

    public function recoverStaminaMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'stamina';
        $method = 'recoverStamina';
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

    public function setMaxValueMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'stamina';
        $method = 'setMaxValue';
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

    public function setMaxValueByStatusMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'stamina';
        $method = 'setMaxValueByStatus';
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

    public function setRecoverIntervalMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'stamina';
        $method = 'setRecoverInterval';
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

    public function setRecoverIntervalByStatusMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'stamina';
        $method = 'setRecoverIntervalByStatus';
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

    public function setRecoverValueMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'stamina';
        $method = 'setRecoverValue';
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

    public function setRecoverValueByStatusMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'stamina';
        $method = 'setRecoverValueByStatus';
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
                    $this->consumeStaminaMetricsView(
                        "hourly",
                    ),
                    $this->recoverStaminaMetricsView(
                        "hourly",
                    ),
                    $this->raiseMaxValueMetricsView(
                        "hourly",
                    ),
                    $this->setMaxValueMetricsView(
                        "hourly",
                    ),
                    $this->setMaxValueByStatusMetricsView(
                        "hourly",
                    ),
                    $this->setRecoverIntervalMetricsView(
                        "hourly",
                    ),
                    $this->setRecoverIntervalByStatusMetricsView(
                        "hourly",
                    ),
                    $this->setRecoverValueMetricsView(
                        "hourly",
                    ),
                    $this->setRecoverValueByStatusMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->consumeStaminaMetricsView(
                        "daily",
                    ),
                    $this->recoverStaminaMetricsView(
                        "daily",
                    ),
                    $this->raiseMaxValueMetricsView(
                        "daily",
                    ),
                    $this->setMaxValueMetricsView(
                        "daily",
                    ),
                    $this->setMaxValueByStatusMetricsView(
                        "daily",
                    ),
                    $this->setRecoverIntervalMetricsView(
                        "daily",
                    ),
                    $this->setRecoverIntervalByStatusMetricsView(
                        "daily",
                    ),
                    $this->setRecoverValueMetricsView(
                        "daily",
                    ),
                    $this->setRecoverValueByStatusMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->consumeStaminaMetricsView(
                        "weekly",
                    ),
                    $this->recoverStaminaMetricsView(
                        "weekly",
                    ),
                    $this->raiseMaxValueMetricsView(
                        "weekly",
                    ),
                    $this->setMaxValueMetricsView(
                        "weekly",
                    ),
                    $this->setMaxValueByStatusMetricsView(
                        "weekly",
                    ),
                    $this->setRecoverIntervalMetricsView(
                        "weekly",
                    ),
                    $this->setRecoverIntervalByStatusMetricsView(
                        "weekly",
                    ),
                    $this->setRecoverValueMetricsView(
                        "weekly",
                    ),
                    $this->setRecoverValueByStatusMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->consumeStaminaMetricsView(
                        "monthly",
                    ),
                    $this->recoverStaminaMetricsView(
                        "monthly",
                    ),
                    $this->raiseMaxValueMetricsView(
                        "monthly",
                    ),
                    $this->setMaxValueMetricsView(
                        "monthly",
                    ),
                    $this->setMaxValueByStatusMetricsView(
                        "monthly",
                    ),
                    $this->setRecoverIntervalMetricsView(
                        "monthly",
                    ),
                    $this->setRecoverIntervalByStatusMetricsView(
                        "monthly",
                    ),
                    $this->setRecoverValueMetricsView(
                        "monthly",
                    ),
                    $this->setRecoverValueByStatusMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
