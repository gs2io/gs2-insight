<?php

namespace App\Domain\Gs2Quest;

use App\Domain\BaseDomain;
use App\Domain\PlayerDomain;
use App\Models\GrnKey;
use Gs2\Core\Exception\NotFoundException;
use Gs2\Core\Net\Gs2RestSession;
use Gs2\Distributor\Gs2DistributorRestClient;
use Gs2\Distributor\Request\RunStampSheetExpressWithoutNamespaceRequest;
use Gs2\Quest\Gs2QuestRestClient;
use Gs2\Quest\Model\Progress;
use Gs2\Quest\Request\DeleteProgressByUserIdRequest;
use Gs2\Quest\Request\EndByUserIdRequest;
use Gs2\Quest\Request\GetProgressByUserIdRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ProgressDomain extends BaseDomain {

    public UserDomain $user;
    public Progress|null $progress;

    public function __construct(
        UserDomain $user,
        Progress|null $progress = null,
    ) {
        $this->user = $user;
        $this->progress = $progress;
    }

    public function complete(
    )
    {
        $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2QuestRestClient(
                    $session,
                );
                $result = $client->endByUserId(
                    (new EndByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withTransactionId($this->model()->getTransactionId())
                        ->withIsComplete(true)
                );
                $stampSheet = $result->getStampSheet();
                $stampSheetEncryptionKeyId = $result->getStampSheetEncryptionKeyId();

                while (true) {
                    $result = (new Gs2DistributorRestClient(
                        $session,
                    ))->runStampSheetExpressWithoutNamespace(
                        (new RunStampSheetExpressWithoutNamespaceRequest())
                            ->withStampSheet($stampSheet)
                            ->withKeyId($stampSheetEncryptionKeyId)
                    );
                    if ($result->getSheetResult() != null) {
                        $response = json_decode($result->getSheetResult(), true);
                        if (in_array('stampSheet', array_keys($response))) {
                            $stampSheet = $response['stampSheet'];
                            $stampSheetEncryptionKeyId = $response['stampSheetEncryptionKeyId'];
                            continue;
                        }
                    }
                    break;
                }
            }
        );
    }

    public function failed(
    )
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2QuestRestClient(
                    $session,
                );
                $result = $client->endByUserId(
                    (new EndByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                        ->withTransactionId($this->model()->getTransactionId())
                        ->withIsComplete(false)
                );
                $stampSheet = $result->getStampSheet();
                $stampSheetEncryptionKeyId = $result->getStampSheetEncryptionKeyId();

                while (true) {
                    $result = (new Gs2DistributorRestClient(
                        $session,
                    ))->runStampSheetExpressWithoutNamespace(
                        (new RunStampSheetExpressWithoutNamespaceRequest())
                            ->withStampSheet($stampSheet)
                            ->withKeyId($stampSheetEncryptionKeyId)
                    );
                    if ($result->getSheetResult() != null) {
                        $response = json_decode($result->getSheetResult(), true);
                        if (in_array('stampSheet', array_keys($response))) {
                            $stampSheet = $response['stampSheet'];
                            $stampSheetEncryptionKeyId = $response['stampSheetEncryptionKeyId'];
                            continue;
                        }
                    }
                    break;
                }
            }
        );
    }

    public function delete(
    ): Progress
    {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2QuestRestClient(
                    $session,
                );
                $result = $client->deleteProgressByUserId(
                    (new DeleteProgressByUserIdRequest())
                        ->withNamespaceName($this->user->namespace->namespaceName)
                        ->withUserId($this->user->userId)
                );
                return $result->getItem();
            }
        );
    }

    public function model(): Progress {
        return $this->gs2(
            function (Gs2RestSession $session) {
                $client = new Gs2QuestRestClient(
                    $session,
                );
                $result = $client->getProgressByUserId(
                    (new GetProgressByUserIdRequest())
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
            $progress = new ProgressDomain(
                $this->user,
                $this->model(),
            );
        } catch (NotFoundException $e) {
            $progress = $this;
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $progress = $this;
        }

        return view($view)
            ->with('progress', $progress);
    }
}
