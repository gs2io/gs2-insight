<?php

namespace App\Domain;

use App\Domain\Gs2Money\ServiceDomain as MoneyServiceDomain;
use App\Domain\Gs2Quest\ServiceDomain as QuestServiceDomain;
use App\Domain\Gs2Stamina\ServiceDomain as StaminaServiceDomain;
use App\Domain\Gs2Mission\ServiceDomain as MissionServiceDomain;
use App\Domain\Gs2Inventory\ServiceDomain as InventoryServiceDomain;
use App\Domain\Gs2Inbox\ServiceDomain as InboxServiceDomain;
use App\Domain\Gs2Datastore\ServiceDomain as DatastoreServiceDomain;
use App\Domain\Gs2Friend\ServiceDomain as FriendServiceDomain;
use App\Domain\Gs2Experience\ServiceDomain as ExperienceServiceDomain;
use App\Domain\Gs2Exchange\ServiceDomain as ExchangeServiceDomain;
use App\Domain\Gs2Showcase\ServiceDomain as ShowcaseServiceDomain;
use App\Domain\Gs2Dictionary\ServiceDomain as DictionaryServiceDomain;
use App\Domain\Gs2Chat\ServiceDomain as ChatServiceDomain;
use App\Domain\Gs2Account\ServiceDomain as AccountServiceDomain;
use App\Domain\Gs2Ranking\ServiceDomain as RankingServiceDomain;
use App\Domain\Gs2JobQueue\ServiceDomain as JobQueueServiceDomain;
use App\Domain\Gs2Limit\ServiceDomain as LimitServiceDomain;
use App\Domain\Gs2Lottery\ServiceDomain as LotteryServiceDomain;
use App\Domain\Gs2Gateway\ServiceDomain as GatewayServiceDomain;
use App\Models\Gcp;
use App\Models\Player;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use JetBrains\PhpStorm\Pure;

class PlayerDomain extends BaseDomain {

    public string $userId;

    public function __construct(
        string $userId,
    ) {
        $this->userId = $userId;
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function model(): Player {
        return Player::query()
            ->where("userId", $this->userId)
            ->first();
    }

    public function infoView(
        string $view,
    ): View {
        return view($view)
            ->with("player", $this)
            ->with("item", $this->model());
    }

    public function stamina(
    ): StaminaServiceDomain {
        return new StaminaServiceDomain();
    }

    public function quest(
    ): QuestServiceDomain {
        return new QuestServiceDomain();
    }

    public function money(
    ): MoneyServiceDomain {
        return new MoneyServiceDomain();
    }

    public function mission(
    ): MissionServiceDomain {
        return new MissionServiceDomain();
    }

    public function inventory(
    ): InventoryServiceDomain {
        return new InventoryServiceDomain();
    }

    public function inbox(
    ): InboxServiceDomain {
        return new InboxServiceDomain();
    }

    public function datastore(
    ): DatastoreServiceDomain {
        return new DatastoreServiceDomain();
    }

    public function friend(
    ): FriendServiceDomain {
        return new FriendServiceDomain();
    }

    public function experience(
    ): ExperienceServiceDomain {
        return new ExperienceServiceDomain();
    }

    public function exchange(
    ): ExchangeServiceDomain {
        return new ExchangeServiceDomain();
    }

    public function showcase(
    ): ShowcaseServiceDomain {
        return new ShowcaseServiceDomain();
    }

    public function dictionary(
    ): DictionaryServiceDomain {
        return new DictionaryServiceDomain();
    }

    public function chat(
    ): ChatServiceDomain {
        return new ChatServiceDomain();
    }

    public function account(
    ): AccountServiceDomain {
        return new AccountServiceDomain();
    }

    public function ranking(
    ): RankingServiceDomain {
        return new RankingServiceDomain();
    }

    public function jobQueue(
    ): JobQueueServiceDomain {
        return new JobQueueServiceDomain();
    }

    public function limit(
    ): LimitServiceDomain {
        return new LimitServiceDomain();
    }

    public function lottery(
    ): LotteryServiceDomain {
        return new LotteryServiceDomain();
    }

    public function gateway(
    ): GatewayServiceDomain {
        return new GatewayServiceDomain();
    }

    #[Pure] public function timeline(
        string $transactionId,
    ): TimelineDomain {
        return new TimelineDomain(
            $this->userId,
            $transactionId,
        );
    }

    public function timelines(
        \DateTime|null $beginAt,
        \DateTime|null $endAt,
        array $actions = null,
    ): Builder {
        return TimelineDomain::list(
            $this->userId,
            $beginAt,
            $endAt,
            $actions,
        );
    }

    public function timelinesView(
        string $view,
        \DateTime $beginAt = null,
        \DateTime $endAt = null,
        array $actions = null,
    ): View {
        $gcp = Gcp::query()->first();
        return view($view)
            ->with("player", $this)
            ->with("gcp", $gcp)
            ->with("actions", $this->timelineActions())
            ->with("timelines",
                $this->timelines(
                    $beginAt,
                    $endAt,
                    $actions,
                )->simplePaginate(10)
            );
    }

    public function timelineActions(
    ): array {
        return TimelineDomain::actions(
            $this->userId,
        );
    }

    public static function list(
        string $userId = null,
        float $purchasedAmountMin = 0,
        float $purchasedAmountMax = PHP_FLOAT_MAX,
    ): Builder {
        $players = Player::query()
            ->orderBy("lastAccessAt", "desc");
        if (!is_null($userId)) {
            $players->where("userId", "like", "%$userId%");
        }
        if (!is_null($purchasedAmountMin) && !is_null($purchasedAmountMax)) {
            $players->whereBetween("purchasedAmount", [$purchasedAmountMin, $purchasedAmountMax]);
        }
        else if (!is_null($purchasedAmountMin) && is_null($purchasedAmountMax)) {
            $players->where("purchasedAmount", '>=', $purchasedAmountMin);
        }
        else if (is_null($purchasedAmountMin) && !is_null($purchasedAmountMax)) {
            $players->where("purchasedAmount", '<=', $purchasedAmountMax);
        }
        return $players;
    }
}
