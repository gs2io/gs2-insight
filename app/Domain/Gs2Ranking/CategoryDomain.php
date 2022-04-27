<?php

namespace App\Domain\Gs2Ranking;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Ranking\Gs2RankingRestClient;
use Gs2\Ranking\Request\GetRankingByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class CategoryDomain extends BaseDomain {

    public UserDomain $user;
    public string $categoryModelName;

    public function __construct(
        UserDomain $user,
        string     $categoryModelName,
    ) {
        $this->user = $user;
        $this->categoryModelName = $categoryModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2RankingRestClient(
                        $session,
                    );
                    $result = $client->getRankingByUserId(
                        (new GetRankingByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                            ->withCategoryName($this->categoryModelName)
                            ->withScorerUserId($this->user->userId)
                            ->withUniqueId(0)
                    );
                    return $result->getItem();
                }
            );

            $ranking = new RankingDomain(
                $this->user,
                $this->categoryModelName,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $ranking = $this;
        }

        return view($view)
            ->with("category", $this)
            ->with("ranking", $ranking);
    }

    public function timeline(): Builder
    {
        return Timeline::query()
            ->joinSub(
                GrnKey::query()
                    ->where('category', 'categoryModel')
                    ->addNestedWhereQuery(
                        GrnKey::query()
                            ->where('grn', '=', "grn:ranking:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:categoryModel:{$this->categoryModelName}")
                            ->orWhere('grn', 'like', "grn:ranking:namespace:{$this->user->namespace->namespaceName}:user:{$this->user->userId}:categoryModel:{$this->categoryModelName}:%")
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
}
