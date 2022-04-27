<?php

namespace App\Domain\Gs2Schedule;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

class TriggerDomain extends BaseDomain {

    public UserDomain $user;
    public string $triggerModelName;

    public function __construct(
        UserDomain $user,
        string     $triggerModelName,
    ) {
        $this->user = $user;
        $this->triggerModelName = $triggerModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("triggerModel", $this);
    }

}
