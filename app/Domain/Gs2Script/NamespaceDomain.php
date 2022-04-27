<?php

namespace App\Domain\Gs2Script;

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

    #[Pure] public function scriptModel(
        string $scriptModelName,
    ): ScriptModelDomain {
        return new ScriptModelDomain(
            $this,
            $scriptModelName
        );
    }

    public function scriptModels(
        string $scriptModelName = null,
    ): Builder {
        $scriptModels = Grn::query()
            ->where("parent", "grn:script:namespace:{$this->namespaceName}")
            ->where("category", "scriptModel");
        if (!is_null($scriptModelName)) {
            $scriptModels->where('key', 'like', "$scriptModelName%");
        }
        return $scriptModels;
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

    public function scriptModelsView(
        string $view,
        string $scriptModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("scriptModels", (
            tap(
                $this->scriptModels(
                    $scriptModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_scriptModels')
            )->transform(
                function ($grn) {
                    return new ScriptModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function invokeMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'script';
        $method = 'invoke';
        $script = 'count';

        return $this->metrics(
            $service,
            $method,
            $script,
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
                    $this->invokeMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->invokeMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->invokeMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->invokeMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
