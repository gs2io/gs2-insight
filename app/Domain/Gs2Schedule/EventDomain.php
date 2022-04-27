<?php

namespace App\Domain\Gs2Schedule;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

class EventDomain extends BaseDomain {

    public UserDomain $user;
    public string $eventModelName;

    public function __construct(
        UserDomain $user,
        string     $eventModelName,
    ) {
        $this->user = $user;
        $this->eventModelName = $eventModelName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("eventModel", $this);
    }

}
