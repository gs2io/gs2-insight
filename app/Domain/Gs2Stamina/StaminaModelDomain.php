<?php

namespace App\Domain\Gs2Stamina;

use App\Domain\BaseDomain;
use App\Models\Grn;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class StaminaModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $staminaModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $staminaModelName,
    ) {
        $this->namespace = $namespace;
        $this->staminaModelName = $staminaModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("staminaModel", $this);
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
                'namespaceName' => $this->namespace->namespaceName,
                'staminaName' => $this->staminaModelName,
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
                'namespaceName' => $this->namespace->namespaceName,
                'staminaName' => $this->staminaModelName,
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
                'namespaceName' => $this->namespace->namespaceName,
                'staminaName' => $this->staminaModelName,
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
                'namespaceName' => $this->namespace->namespaceName,
                'staminaName' => $this->staminaModelName,
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
                'namespaceName' => $this->namespace->namespaceName,
                'staminaName' => $this->staminaModelName,
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
                'namespaceName' => $this->namespace->namespaceName,
                'staminaName' => $this->staminaModelName,
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
                'namespaceName' => $this->namespace->namespaceName,
                'staminaName' => $this->staminaModelName,
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
                'namespaceName' => $this->namespace->namespaceName,
                'staminaName' => $this->staminaModelName,
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
                'namespaceName' => $this->namespace->namespaceName,
                'staminaName' => $this->staminaModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("staminaModel", $this)
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
