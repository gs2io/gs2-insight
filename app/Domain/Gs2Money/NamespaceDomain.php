<?php

namespace App\Domain\Gs2Money;

use App\Domain\BaseDomain;
use App\Models\Grn;
use App\Models\Player;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class NamespaceDomain extends BaseDomain {

    public string $namespaceName;

    public function __construct(
        string $namespaceName,
    ) {
        $this->namespaceName = $namespaceName;
    }

    #[Pure] public function content(
        string $contentId,
    ): ContentDomain {
        return new ContentDomain(
            $this,
            $contentId,
        );
    }

    public function contents(
        string $contentId = null,
    ): Builder {
        $contents = Grn::query()
            ->where("parent", "grn:money:namespace:{$this->namespaceName}")
            ->where("category", "contents");
        if (!is_null($contentId)) {
            $contents->where('key', 'like', "$contentId%");
        }
        return $contents;
    }

    #[Pure] public function user(
        string $userId,
    ): UserDomain {
        return new UserDomain(
            $this,
            $userId,
        );
    }

    public function users(
        string $userId = null,
    ): Builder {
        $users = Player::query();
        if (!is_null($userId)) {
            $users->where('userId', 'like', "$userId%");
        }
        return $users;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("namespace", $this);
    }

    public function contentsView(
        string $view,
        string $contentName = null,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with("contents", (
            tap(
                $this->contents(
                    $contentName,
                )
                    ->simplePaginate(10, ['*'], 'namespace_contents')
            )->transform(
                function ($grn) {
                    return new ContentDomain(
                        $this,
                        $grn->key,
                    );
                }
            )
            ));
    }

    public function depositMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'money';
        $method = 'deposit';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function withdrawMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'money';
        $method = 'withdraw';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function recordReceiptMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'money';
        $method = 'recordReceipt';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function depositSumMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'money';
        $method = 'deposit';
        $category = 'sum:count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function withdrawSumMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'money';
        $method = 'withdraw';
        $category = 'sum:count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespaceName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("namespace", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->depositMetricsView(
                        "hourly",
                    ),
                    $this->withdrawMetricsView(
                        "hourly",
                    ),
                    $this->recordReceiptMetricsView(
                        "hourly",
                    ),
                    $this->depositSumMetricsView(
                        "hourly",
                    ),
                    $this->withdrawSumMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->depositMetricsView(
                        "daily",
                    ),
                    $this->withdrawMetricsView(
                        "daily",
                    ),
                    $this->recordReceiptMetricsView(
                        "daily",
                    ),
                    $this->depositSumMetricsView(
                        "daily",
                    ),
                    $this->withdrawSumMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->depositMetricsView(
                        "weekly",
                    ),
                    $this->withdrawMetricsView(
                        "weekly",
                    ),
                    $this->recordReceiptMetricsView(
                        "weekly",
                    ),
                    $this->depositSumMetricsView(
                        "weekly",
                    ),
                    $this->withdrawSumMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->depositMetricsView(
                        "monthly",
                    ),
                    $this->withdrawMetricsView(
                        "monthly",
                    ),
                    $this->recordReceiptMetricsView(
                        "monthly",
                    ),
                    $this->depositSumMetricsView(
                        "monthly",
                    ),
                    $this->withdrawSumMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
