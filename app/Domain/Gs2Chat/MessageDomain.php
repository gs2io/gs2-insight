<?php

namespace App\Domain\Gs2Chat;

use App\Domain\BaseDomain;
use Gs2\Chat\Gs2ChatRestClient;
use Gs2\Chat\Model\Message;
use Gs2\Chat\Request\GetMessageByUserIdRequest;
use Gs2\Core\Net\Gs2RestSession;
use Illuminate\Contracts\View\View;

class MessageDomain extends BaseDomain {

    public RoomByUserDomain $room;
    public string $messageName;

    public function __construct(
        RoomByUserDomain $room,
        string $messageName,
    ) {
        $this->room = $room;
        $this->messageName = $messageName;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("message", $this);
    }

}
