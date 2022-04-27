<?php

namespace App\Domain\Gs2Experience;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

class ExperienceModelDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $experienceModelName;

    public function __construct(
        NamespaceDomain $namespace,
        string $experienceModelName,
    ) {
        $this->namespace = $namespace;
        $this->experienceModelName = $experienceModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("experienceModel", $this);
    }

    public function addExperienceMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'experience';
        $method = 'addExperience';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'experienceName' => $this->experienceModelName,
            ]
        );
    }

    public function addRankCapMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'experience';
        $method = 'addRankCap';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'experienceName' => $this->experienceModelName,
            ]
        );
    }

    public function setExperienceMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'experience';
        $method = 'setExperience';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'experienceName' => $this->experienceModelName,
            ]
        );
    }

    public function setRankCapMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'experience';
        $method = 'setRankCap';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'experienceName' => $this->experienceModelName,
            ]
        );
    }

    public function addExperienceSumMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'experience';
        $method = 'addExperience';
        $category = 'sum:experienceValue';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'experienceName' => $this->experienceModelName,
            ]
        );
    }

    public function addRankCapSumMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'experience';
        $method = 'addRankCap';
        $category = 'sum:rankCapValue';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'experienceName' => $this->experienceModelName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("experienceModel", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->addExperienceMetricsView(
                        "hourly",
                    ),
                    $this->addRankCapMetricsView(
                        "hourly",
                    ),
                    $this->setExperienceMetricsView(
                        "hourly",
                    ),
                    $this->setRankCapMetricsView(
                        "hourly",
                    ),
                    $this->addExperienceSumMetricsView(
                        "hourly",
                    ),
                    $this->addRankCapSumMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->addExperienceMetricsView(
                        "daily",
                    ),
                    $this->addRankCapMetricsView(
                        "daily",
                    ),
                    $this->setExperienceMetricsView(
                        "daily",
                    ),
                    $this->setRankCapMetricsView(
                        "daily",
                    ),
                    $this->addExperienceSumMetricsView(
                        "daily",
                    ),
                    $this->addRankCapSumMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->addExperienceMetricsView(
                        "weekly",
                    ),
                    $this->addRankCapMetricsView(
                        "weekly",
                    ),
                    $this->setExperienceMetricsView(
                        "weekly",
                    ),
                    $this->setRankCapMetricsView(
                        "weekly",
                    ),
                    $this->addExperienceSumMetricsView(
                        "weekly",
                    ),
                    $this->addRankCapSumMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->addExperienceMetricsView(
                        "monthly",
                    ),
                    $this->addRankCapMetricsView(
                        "monthly",
                    ),
                    $this->setExperienceMetricsView(
                        "monthly",
                    ),
                    $this->setRankCapMetricsView(
                        "monthly",
                    ),
                    $this->addExperienceSumMetricsView(
                        "monthly",
                    ),
                    $this->addRankCapSumMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
