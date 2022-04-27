<?php

namespace App\Domain\Gs2Ranking;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Ranking\Gs2RankingRestClient;
use Gs2\Ranking\Model\Score;
use Gs2\Ranking\Request\DescribeScoresByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class UserDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $userId;

    public function __construct(
        NamespaceDomain $namespace,
        string $userId,
    ) {
        $this->namespace = $namespace;
        $this->userId = $userId;
    }

    #[Pure] public function category(
        string $categoryModelName,
    ): CategoryDomain {
        return new CategoryDomain(
            $this,
            $categoryModelName
        );
    }

    public function categories(
        string $categoryModelName = null,
    ): Builder {
        $categories = Grn::query()
            ->where("parent", "grn:ranking:namespace:{$this->namespace->namespaceName}")
            ->where("category", "categoryModel");
        if (!is_null($categoryModelName)) {
            $categories->where('key', 'like', "$categoryModelName%");
        }
        return $categories;
    }

    public function currentScores(
    ): array {
        try {
            return $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2RankingRestClient(
                        $session,
                    );
                    $result = $client->describeScoresByUserId(
                        (new DescribeScoresByUserIdRequest())
                            ->withNamespaceName($this->namespace->namespaceName)
                            ->withUserId($this->userId)
                            ->withScorerUserId($this->userId)
                    );
                    return array_map(
                        function (Score $item) {
                            return new ScoreDomain(
                                $this,
                                $item->getCategoryName(),
                                $item,
                            );
                        }
                        , $result->getItems());
                }
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return [];
    }

    public function categoriesView(
        string $view,
        string $categoryModelName = null,
    ): View
    {
        return view($view)
            ->with("user", $this)
            ->with("categories", (
            tap(
                $this->categories(
                    $categoryModelName,
                )
                    ->simplePaginate(10, ['*'], 'user_categories')
            )->transform(
                function ($grn) {
                    return new CategoryDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function currentScoresView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this)
            ->with("scores", $this->currentScores());
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'categoryModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:ranking:namespace:{$this->namespace->namespaceName}:user:{$this->userId}")
                            ->orWhere('grn', 'like', "grn:ranking:namespace:{$this->namespace->namespaceName}:user:{$this->userId}:%")
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
            ->with("user", $this)
            ->with("timeline", $this->timeline()->simplePaginate(10, ['*'], 'user_timeline'));
    }

    public function rankingControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
