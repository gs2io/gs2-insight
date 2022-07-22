<?php

namespace App\Domain\Gs2Account;

use App\Domain\BaseDomain;
use Gs2\Account\Gs2AccountRestClient;
use Gs2\Account\Model\DataOwner;
use Gs2\Account\Request\DeleteDataOwnerByUserIdRequest;
use Gs2\Account\Request\GetDataOwnerByUserIdRequest;
use Gs2\Core\Net\Gs2RestSession;
use Illuminate\Contracts\View\View;

class DataOwnerDomain extends BaseDomain {

    public UserDomain $user;
    public DataOwner|null $dataOwner;

    public function __construct(
        UserDomain $user,
        DataOwner $dataOwner = null,
    ) {
        $this->user = $user;
        $this->dataOwner = $dataOwner;
    }

    public function delete(): DataOwner
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2AccountRestClient(
                    $session,
                );
                $result = $client->deleteDataOwnerByUserId(
                    (new DeleteDataOwnerByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                );
                return $result->getItem();
            }
        );
    }

    public function infoView(
        string $view,
    ): View
    {
        try {
            $item = $this->gs2(
                function (Gs2RestSession $session) {
                    $client = new Gs2AccountRestClient(
                        $session,
                    );
                    $result = $client->getDataOwnerByUserId(
                        (new GetDataOwnerByUserIdRequest())
                            ->withNamespaceName($this->user->namespace->namespaceName)
                            ->withUserId($this->user->userId)
                    );
                    return $result->getItem();
                }
            );
            $dataOwner = new DataOwnerDomain(
                $this->user,
                $item,
            );
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $dataOwner = $this;
        }

        return view($view)
            ->with('dataOwner', $dataOwner);
    }
}
