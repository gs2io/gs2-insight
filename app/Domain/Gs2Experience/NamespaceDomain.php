<?php

namespace App\Domain\Gs2Experience;

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

    #[Pure] public function experienceModel(
        string $experienceModelName,
    ): ExperienceModelDomain {
        return new ExperienceModelDomain(
            $this,
            $experienceModelName
        );
    }

    public function experienceModels(
        string $experienceModelName = null,
    ): Builder {
        $experienceModels = Grn::query()
            ->where("parent", "grn:experience:namespace:{$this->namespaceName}")
            ->where("category", "experienceModel");
        if (!is_null($experienceModelName)) {
            $experienceModels->where('key', 'like', "$experienceModelName%");
        }
        return $experienceModels;
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

    public function experienceModelsView(
        string $view,
        string $experienceModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("experienceModels", (
            tap(
                $this->experienceModels(
                    $experienceModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_experienceModels')
            )->transform(
                function ($grn) {
                    return new ExperienceModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
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
                'namespaceName' => $this->namespaceName,
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
                'namespaceName' => $this->namespaceName,
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
                'namespaceName' => $this->namespaceName,
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
                ],
            ]);
    }
}
