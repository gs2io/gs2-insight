<?php

namespace App\Domain\Gs2Money;

use App\Domain\BaseDomain;
use Illuminate\Contracts\View\View;

class ContentDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $contentId;

    public function __construct(
        NamespaceDomain $namespace,
        string $contentId,
    ) {
        $this->namespace = $namespace;
        $this->contentId = $contentId;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("content", $this);
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
                'namespaceName' => $this->namespace->namespaceName,
                'contentsId' => $this->contentId,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("content", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->recordReceiptMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->recordReceiptMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->recordReceiptMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->recordReceiptMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
