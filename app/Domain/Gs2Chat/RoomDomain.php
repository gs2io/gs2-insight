<?php

namespace App\Domain\Gs2Chat;

use App\Domain\BaseDomain;
use App\Domain\PlayerDomain;
use App\Models\Grn;
use App\Models\GrnKey;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\Pure;

class RoomDomain extends BaseDomain {

    public NamespaceDomain $namespace;
    public string $roomName;

    public function __construct(
        NamespaceDomain $namespace,
        string $roomName,
    ) {
        $this->namespace = $namespace;
        $this->roomName = $roomName;
    }

    #[Pure] public function message(
        string $messageName,
    ): MessageDomain {
        return new MessageDomain(
            $this,
            $messageName,
        );
    }

    public function messages(
        string $messageName = null,
    ): Builder {
        $messages = Grn::query()
            ->where("parent", "grn:chat:namespace:{$this->namespace->namespaceName}:room:{$this->roomName}")
            ->where("category", "message");
        if (!is_null($messageName)) {
            $messages->where('key', 'like', "$messageName%");
        }
        return $messages;
    }

    public function infoView(
        string $view,
    ): View
    {
        return view($view)
            ->with("room", $this);
    }

    public function messagesView(
        string $view,
    ): View
    {
        return view($view)
            ->with("messages", $this->messages());
    }

    public function describeMessagesMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'chat';
        $method = 'describeMessages';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'roomName' => $this->roomName,
            ]
        );
    }

    public function postMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'chat';
        $method = 'post';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'roomName' => $this->roomName,
            ]
        );
    }

    public function subscribeMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'chat';
        $method = 'subscribe';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'roomName' => $this->roomName,
            ]
        );
    }

    public function unsubscribeMetricsView(
        string $timeSpan,
    ): View
    {
        $service = 'chat';
        $method = 'unsubscribe';
        $category = 'count';

        return $this->metrics(
            $service,
            $method,
            $category,
            $timeSpan,
            [
                'namespaceName' => $this->namespace->namespaceName,
                'roomName' => $this->roomName,
            ]
        );
    }

    public function metricsView(
        string $view,
    ): View
    {
        return view($view)
            ->with("room", $this)
            ->with('metrics', [
                "hourly" => [
                    $this->describeMessagesMetricsView(
                        "hourly",
                    ),
                    $this->postMetricsView(
                        "hourly",
                    ),
                    $this->subscribeMetricsView(
                        "hourly",
                    ),
                    $this->unsubscribeMetricsView(
                        "hourly",
                    ),
                ],
                "daily" => [
                    $this->describeMessagesMetricsView(
                        "daily",
                    ),
                    $this->postMetricsView(
                        "daily",
                    ),
                    $this->subscribeMetricsView(
                        "daily",
                    ),
                    $this->unsubscribeMetricsView(
                        "daily",
                    ),
                ],
                "weekly" => [
                    $this->describeMessagesMetricsView(
                        "weekly",
                    ),
                    $this->postMetricsView(
                        "weekly",
                    ),
                    $this->subscribeMetricsView(
                        "weekly",
                    ),
                    $this->unsubscribeMetricsView(
                        "weekly",
                    ),
                ],
                "monthly" => [
                    $this->describeMessagesMetricsView(
                        "monthly",
                    ),
                    $this->postMetricsView(
                        "monthly",
                    ),
                    $this->subscribeMetricsView(
                        "monthly",
                    ),
                    $this->unsubscribeMetricsView(
                        "monthly",
                    ),
                ],
            ]);
    }
}
