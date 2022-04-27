<?php

namespace App\Domain\Gs2Ranking;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Ranking\Model\Ranking;
use Gs2\Ranking\Model\Score;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class RankingDomain extends BaseDomain {

    public UserDomain $user;
    public string $categoryModelName;
    public Ranking|null $ranking;

    public function __construct(
        UserDomain $user,
        string     $categoryModelName,
        Ranking|null $ranking = null,
    ) {
        $this->user = $user;
        $this->categoryModelName = $categoryModelName;
        $this->ranking = $ranking;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("ranking", $this);
    }
}
