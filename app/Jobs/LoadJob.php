<?php

namespace App\Jobs;

use App\Domain\Aggregate\Metrics\ActiveUsers;
use App\Domain\Aggregate\Metrics\Version\Index;
use App\Domain\Aggregate\Players;
use App\Domain\Aggregate\Timelines;
use App\Domain\GcpDomain;
use App\Http\Controllers\Enums\LoadTarget;
use App\Models\LoadStatus;
use DateInterval;
use DatePeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private LoadTarget $loadTarget;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LoadTarget $loadTarget)
    {
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
                $timelines->load();
                break;
            case LoadTarget::Player;
                $players = new Players(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $players->load();
                break;
            case LoadTarget::ActiveUser;
                $timelines = new ActiveUsers(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;

            case LoadTarget::Account;
                $timelines = new \App\Domain\Aggregate\Metrics\Account\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Chat;
                $timelines = new \App\Domain\Aggregate\Metrics\Chat\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Datastore;
                $timelines = new \App\Domain\Aggregate\Metrics\Datastore\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Dictionary;
                $timelines = new \App\Domain\Aggregate\Metrics\Dictionary\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Exchange;
                $timelines = new \App\Domain\Aggregate\Metrics\Exchange\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Experience;
                $timelines = new \App\Domain\Aggregate\Metrics\Experience\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Friend;
                $timelines = new \App\Domain\Aggregate\Metrics\Friend\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Gateway;
                $timelines = new \App\Domain\Aggregate\Metrics\Gateway\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Inbox;
                $timelines = new \App\Domain\Aggregate\Metrics\Inbox\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Inventory;
                $timelines = new \App\Domain\Aggregate\Metrics\Inventory\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::JobQueue;
                $timelines = new \App\Domain\Aggregate\Metrics\JobQueue\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Limit;
                $timelines = new \App\Domain\Aggregate\Metrics\Limit\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Lottery;
                $timelines = new \App\Domain\Aggregate\Metrics\Lottery\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Matchmaking;
                $timelines = new \App\Domain\Aggregate\Metrics\Matchmaking\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Mission;
                $timelines = new \App\Domain\Aggregate\Metrics\Mission\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Money;
                $timelines = new \App\Domain\Aggregate\Metrics\Money\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Quest;
                $timelines = new \App\Domain\Aggregate\Metrics\Quest\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Ranking;
                $timelines = new \App\Domain\Aggregate\Metrics\Ranking\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Realtime;
                $timelines = new \App\Domain\Aggregate\Metrics\Realtime\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Schedule;
                $timelines = new \App\Domain\Aggregate\Metrics\Schedule\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Script;
                $timelines = new \App\Domain\Aggregate\Metrics\Script\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Showcase;
                $timelines = new \App\Domain\Aggregate\Metrics\Showcase\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Stamina;
                $timelines = new \App\Domain\Aggregate\Metrics\Stamina\Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;
            case LoadTarget::Version;
                $timelines = new Index(
                    $period,
                    $gcp->datasetName,
                    $gcp->credentials,
                );
                $timelines->load();
                break;

            case LoadTarget::Done;
                $nextStatus = null;
                break;
        }
    }
}
