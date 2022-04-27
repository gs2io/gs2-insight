<?php

namespace App\Domain\Gs2Realtime;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

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

    public function realtimeControllerView(
        string $view,
    ): View
    {
        return view($view)
            ->with('user', $this);
    }

}
