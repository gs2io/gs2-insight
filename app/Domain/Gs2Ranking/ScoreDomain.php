<?php

namespace App\Domain\Gs2Ranking;

use App\Domain\BaseDomain;
use App\Models\GrnKey;
use App\Models\Timeline;
use Gs2\Ranking\Model\Score;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ScoreDomain extends BaseDomain {

    public UserDomain $user;
    public string $categoryModelName;
    public Score|null $score;

    public function __construct(
        UserDomain $user,
        string     $categoryModelName,
        Score|null $score = null,
    ) {
        $this->user = $user;
        $this->categoryModelName = $categoryModelName;
        $this->score = $score;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("score", $this);
    }
}
