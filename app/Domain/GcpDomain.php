<?php

namespace App\Domain;

use App\Domain\Gs2Money\ServiceDomain as MoneyServiceDomain;
use App\Domain\Gs2Quest\ServiceDomain as QuestServiceDomain;
use App\Domain\Gs2Stamina\ServiceDomain as StaminaServiceDomain;
use App\Domain\Gs2Mission\ServiceDomain as MissionServiceDomain;
use App\Domain\Gs2Inventory\ServiceDomain as InventoryServiceDomain;
use App\Domain\Gs2Inbox\ServiceDomain as InboxServiceDomain;
use App\Domain\Gs2Friend\ServiceDomain as FriendServiceDomain;
use App\Domain\Gs2Experience\ServiceDomain as ExperienceServiceDomain;
use App\Domain\Gs2Exchange\ServiceDomain as ExchangeServiceDomain;
use App\Domain\Gs2Showcase\ServiceDomain as ShowcaseServiceDomain;
use App\Domain\Gs2Dictionary\ServiceDomain as DictionaryServiceDomain;
use App\Domain\Gs2Chat\ServiceDomain as ChatServiceDomain;
use App\Domain\Gs2Account\ServiceDomain as AccountServiceDomain;
use App\Models\Gcp;
use App\Models\Player;
use Illuminate\Database\Eloquent\Builder;

class GcpDomain extends BaseDomain {

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function model(): Gcp|null {
        return Gcp::query()
            ->first();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public static function create(
        string $datasetName,
        int $startAt,
        int $endAt,
        string $credentials,
    ): Gcp {
        Gcp::query()->updateOrCreate(
            [
                "datasetName" => $datasetName,
                "beginAt" => $startAt,
                "endAt" => $endAt,
                "credentials" => $credentials,
            ],
        );
        return new Gcp();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public static function update(
        int $startAt,
        int $endAt,
    ): Gcp {
        $gcp = Gcp::query()
            ->first();
        $gcp->update([
            "beginAt" => $startAt,
            "endAt" => $endAt,
        ]);
        return new Gcp();
    }
}
