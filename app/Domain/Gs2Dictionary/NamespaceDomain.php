<?php

namespace App\Domain\Gs2Dictionary;

use App\Domain\BaseDomain;
use App\Domain\Gs2Chat\RoomDomain;
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

    #[Pure] public function entryModel(
        string $entryModelName,
    ): EntryModelDomain {
        return new EntryModelDomain(
            $this,
            $entryModelName
        );
    }

    public function entryModels(
        string $entryModelName = null,
    ): Builder {
        $entryModels = Grn::query()
            ->where("parent", "grn:dictionary:namespace:{$this->namespaceName}")
            ->where("category", "entryModel");
        if (!is_null($entryModelName)) {
            $entryModels->where('key', 'like', "$entryModelName%");
        }
        return $entryModels;
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

    public function entryModelsView(
        string $view,
        string $entryModelName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("entryModels", (
            tap(
                $this->entryModels(
                    $entryModelName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_entryModels')
            )->transform(
                function ($grn) {
                    return new EntryModelDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function addEntriesMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'dictionary';
        $method = 'addEntries';
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
                    $this->addEntriesMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->addEntriesMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->addEntriesMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->addEntriesMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
