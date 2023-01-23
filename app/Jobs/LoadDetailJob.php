<?php

namespace App\Jobs;

use App\Domain\Aggregate\Metrics\Version\GrnKey;
use App\Domain\Aggregate\Timelines;
use App\Domain\GcpDomain;
use App\Domain\PlayerDomain;
use App\Http\Controllers\Enums\LoadTarget;
use DateInterval;
use DatePeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadDetailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $userId;
    private LoadTarget $loadTarget;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $userId, LoadTarget $loadTarget)
    {
        $this->userId = $userId;
        $this->loadTarget = $loadTarget;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $gcp = (new GcpDomain())->model();
        $period = new DatePeriod(
            $gcp->beginAt->setTimezone('UTC'),
            DateInterval::createFromDateString("1 second"),
            $gcp->endAt->setTimezone('UTC'),
        );

        switch ($this->loadTarget) {
            case LoadTarget::Timeline;
                $timelines = new Timelines(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->loadDetail($this->userId);

                $player = (new PlayerDomain($this->userId))->model();
                $player->fetched($gcp->beginAt, $gcp->endAt);
                break;

            case LoadTarget::Account;
                $timelines = new \App\Domain\Aggregate\Metrics\Account\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Chat;
                $timelines = new \App\Domain\Aggregate\Metrics\Chat\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Datastore;
                $timelines = new \App\Domain\Aggregate\Metrics\Datastore\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Dictionary;
                $timelines = new \App\Domain\Aggregate\Metrics\Dictionary\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Exchange;
                $timelines = new \App\Domain\Aggregate\Metrics\Exchange\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Experience;
                $timelines = new \App\Domain\Aggregate\Metrics\Experience\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Friend;
                $timelines = new \App\Domain\Aggregate\Metrics\Friend\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Gateway;
                $timelines = new \App\Domain\Aggregate\Metrics\Gateway\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Inbox;
                $timelines = new \App\Domain\Aggregate\Metrics\Inbox\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Inventory;
                $timelines = new \App\Domain\Aggregate\Metrics\Inventory\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::JobQueue;
                $timelines = new \App\Domain\Aggregate\Metrics\JobQueue\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Limit;
                $timelines = new \App\Domain\Aggregate\Metrics\Limit\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Lottery;
                $timelines = new \App\Domain\Aggregate\Metrics\Lottery\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Matchmaking;
                $timelines = new \App\Domain\Aggregate\Metrics\Matchmaking\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Mission;
                $timelines = new \App\Domain\Aggregate\Metrics\Mission\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Money;
                $timelines = new \App\Domain\Aggregate\Metrics\Money\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Quest;
                $timelines = new \App\Domain\Aggregate\Metrics\Quest\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Ranking;
                $timelines = new \App\Domain\Aggregate\Metrics\Ranking\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Realtime;
                $timelines = new \App\Domain\Aggregate\Metrics\Realtime\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Schedule;
                $timelines = new \App\Domain\Aggregate\Metrics\Schedule\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Script;
                $timelines = new \App\Domain\Aggregate\Metrics\Script\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Showcase;
                $timelines = new \App\Domain\Aggregate\Metrics\Showcase\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Stamina;
                $timelines = new \App\Domain\Aggregate\Metrics\Stamina\GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
            case LoadTarget::Version;
                $timelines = new GrnKey(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load($this->userId);
                break;
        }
    }
}
