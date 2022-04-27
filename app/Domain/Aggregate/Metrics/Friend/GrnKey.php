<?php

namespace App\Domain\Aggregate\Metrics\Friend;

use App\Domain\Aggregate\Metrics\AbstractMetrics;
use App\Domain\Aggregate\Metrics\Common\GrnKeyLoader;
use App\Domain\Aggregate\Metrics\Common\GrnLoader;
use App\Domain\Aggregate\Metrics\Common\Result\LoadResult;
use DatePeriod;
use JetBrains\PhpStorm\Pure;

class GrnKey extends AbstractMetrics
{
    #[Pure] public function __construct(
        DatePeriod $period,
        string $datasetName,
        string $credentials,
    )
    {
        parent::__construct(
            $period,
            $datasetName,
            $credentials,
        );
    }

    public function load(
        string $userId,
    ): LoadResult {
        $service = 'friend';

        $totalBytesProcessed = 0;

        $grns = \App\Models\Grn::query()
            ->where('parent', "grn:friend")
            ->where('category', "namespace")
            ->get();
        foreach ($grns as $grn) {
            $namespaceName = $grn->key;

            $grnKeys = GrnKeyLoader::load(
                $this->createClient(),
                new \App\Models\Grn([
                    'grn' => "{$grn['grn']}:user:$userId",
                    'parent' => $grn['grn'],
                    'key' => $userId,
                ]),
                GrnKeyLoader::buildQuery(
                    $service,
                    ['friend', 'item.userId'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                    [
                        'deleteFriend',
                        'deleteFriendByUserId',
                        'acceptRequest',
                        'acceptRequestByUserId',
                        'rejectRequest',
                        'rejectRequestByUserId',
                        'sendRequest',
                        'sendRequestByUserId',
                        'deleteRequest',
                        'deleteRequestByUserId',
                    ],
                ),
                'friend',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $friendModels = GrnLoader::load(
                $this->createClient(),
                new \App\Models\Grn([
                    'grn' => "{$grn['grn']}:user:$userId",
                    'parent' => $grn['grn'],
                    'key' => $userId,
                ]),
                GrnLoader::buildQuery(
                    $service,
                    'targetUserId',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                        'userId' => $userId,
                    ],
                    [
                        'deleteFriend',
                        'deleteFriendByUserId',
                        'acceptRequest',
                        'acceptRequestByUserId',
                        'rejectRequest',
                        'rejectRequestByUserId',
                        'sendRequest',
                        'sendRequestByUserId',
                        'deleteRequest',
                        'deleteRequestByUserId',
                    ],
                ),
                'friend',
            );
            $totalBytesProcessed += $friendModels->totalBytesProcessed;

            $grnKeys = GrnKeyLoader::load(
                $this->createClient(),
                new \App\Models\Grn([
                    'grn' => "{$grn['grn']}:user:$userId",
                    'parent' => $grn['grn'],
                    'key' => $userId,
                ]),
                GrnKeyLoader::buildQuery(
                    $service,
                    ['follower', 'item.userId'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                    [
                        'follow',
                        'followByUserId',
                        'unfollow',
                        'unfollowByUserId',
                    ],
                ),
                'follower',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;

            $friendModels = GrnLoader::load(
                $this->createClient(),
                new \App\Models\Grn([
                    'grn' => "{$grn['grn']}:user:$userId",
                    'parent' => $grn['grn'],
                    'key' => $userId,
                ]),
                GrnLoader::buildQuery(
                    $service,
                    'targetUserId',
                    $this->table(),
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                    [
                        'follow',
                        'followByUserId',
                        'unfollow',
                        'unfollowByUserId',
                    ],
                    $userId,
                ),
                'follower',
            );
            $totalBytesProcessed += $friendModels->totalBytesProcessed;

            $grnKeys = GrnKeyLoader::load(
                $this->createClient(),
                new \App\Models\Grn([
                    'grn' => "{$grn['grn']}:user:$userId",
                    'parent' => $grn['grn'],
                    'key' => $userId,
                ]),
                GrnKeyLoader::buildQuery(
                    $service,
                    ['profile', 'item.userId'],
                    $this->table(),
                    $userId,
                    $this->timeRange(),
                    [
                        'namespaceName' => $namespaceName,
                    ],
                    [
                        'updateProfile',
                        'updateProfileByUserId',
                        'deleteProfile',
                        'deleteProfileByUserId',
                    ],
                ),
                'profile',
            );
            $totalBytesProcessed += $grnKeys->totalBytesProcessed;
        }
        return new LoadResult(
            $totalBytesProcessed,
        );
    }
}
