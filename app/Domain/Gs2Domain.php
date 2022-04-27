<?php

namespace App\Domain;

use App\Models\Gs2;

class Gs2Domain extends BaseDomain {

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public function model(): Gs2|null {
        return Gs2::query()
            ->first();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public static function create(
        string $clientId,
        string $clientSecret,
        string $region,
        string $permission,
    ): Gs2 {
        Gs2::query()->updateOrCreate(
            [
                "clientId" => $clientId,
                "clientSecret" => $clientSecret,
                "region" => $region,
                "permission" => $permission,
            ],
        );
        return new Gs2();
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    public static function update(
        string $region,
        string $permission,
    ): Gs2 {
        $gs2 = Gs2::query()
            ->first();
        $gs2->update([
            "region" => $region,
            "permission" => $permission,
        ]);
        return new Gs2();
    }

    public function permission(): string {
        $gs2 = $this->model();
        if ($gs2 == null) {
            return "null";
        }
        return $gs2->permission;
    }
}
