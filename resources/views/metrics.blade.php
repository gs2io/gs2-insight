@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url()->current(),
        ]
    ])
    <div class="m-4 bg-white p-6 shadow-sm rounded-lg justify-between items-center">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="hourly-tab" data-bs-toggle="tab" data-bs-target="#hourly" type="button" role="tab">
                    Hourly
                </button>
                <button class="nav-link" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily" type="button" role="tab">
                    Daily
                </button>
                <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab">
                    Monthly
                </button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane show active" id="hourly" role="tabpanel">
                {{ \App\Http\Controllers\Metrics\ActiveUserController::load('hourly') }}
            </div>
            <div class="tab-pane" id="daily" role="tabpanel">
                {{ \App\Http\Controllers\Metrics\ActiveUserController::load('daily') }}
            </div>
            <div class="tab-pane" id="monthly" role="tabpanel">
                {{ \App\Http\Controllers\Metrics\ActiveUserController::load('monthly') }}
            </div>
        </div>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="Account-tab" data-bs-toggle="tab" data-bs-target="#Account" type="button" role="tab">
                    Account
                </button>
                <button class="nav-link" id="Chat-tab" data-bs-toggle="tab" data-bs-target="#Chat" type="button" role="tab">
                    Chat
                </button>
                <button class="nav-link" id="Datastore-tab" data-bs-toggle="tab" data-bs-target="#Datastore" type="button" role="tab">
                    Datastore
                </button>
                <button class="nav-link" id="Dictionary-tab" data-bs-toggle="tab" data-bs-target="#Dictionary" type="button" role="tab">
                    Dictionary
                </button>
                <button class="nav-link" id="Exchange-tab" data-bs-toggle="tab" data-bs-target="#Exchange" type="button" role="tab">
                    Exchange
                </button>
                <button class="nav-link" id="Experience-tab" data-bs-toggle="tab" data-bs-target="#Experience" type="button" role="tab">
                    Experience
                </button>
                <button class="nav-link" id="Friend-tab" data-bs-toggle="tab" data-bs-target="#Friend" type="button" role="tab">
                    Friend
                </button>
                <button class="nav-link" id="Inbox-tab" data-bs-toggle="tab" data-bs-target="#Inbox" type="button" role="tab">
                    Inbox
                </button>
                <button class="nav-link" id="Inventory-tab" data-bs-toggle="tab" data-bs-target="#Inventory" type="button" role="tab">
                    Inventory
                </button>
                <button class="nav-link" id="JobQueue-tab" data-bs-toggle="tab" data-bs-target="#JobQueue" type="button" role="tab">
                    JobQueue
                </button>
                <button class="nav-link" id="Limit-tab" data-bs-toggle="tab" data-bs-target="#Limit" type="button" role="tab">
                    Limit
                </button>
                <button class="nav-link" id="Lottery-tab" data-bs-toggle="tab" data-bs-target="#Lottery" type="button" role="tab">
                    Lottery
                </button>
                <button class="nav-link" id="Matchmaking-tab" data-bs-toggle="tab" data-bs-target="#Matchmaking" type="button" role="tab">
                    Matchmaking
                </button>
                <button class="nav-link" id="Mission-tab" data-bs-toggle="tab" data-bs-target="#Mission" type="button" role="tab">
                    Mission
                </button>
                <button class="nav-link" id="Money-tab" data-bs-toggle="tab" data-bs-target="#Money" type="button" role="tab">
                    Money
                </button>
                <button class="nav-link" id="Quest-tab" data-bs-toggle="tab" data-bs-target="#Quest" type="button" role="tab">
                    Quest
                </button>
                <button class="nav-link" id="Ranking-tab" data-bs-toggle="tab" data-bs-target="#Ranking" type="button" role="tab">
                    Ranking
                </button>
                <button class="nav-link" id="Realtime-tab" data-bs-toggle="tab" data-bs-target="#Realtime" type="button" role="tab">
                    Realtime
                </button>
                <button class="nav-link" id="Schedule-tab" data-bs-toggle="tab" data-bs-target="#Schedule" type="button" role="tab">
                    Schedule
                </button>
                <button class="nav-link" id="Script-tab" data-bs-toggle="tab" data-bs-target="#Script" type="button" role="tab">
                    Script
                </button>
                <button class="nav-link" id="Showcase-tab" data-bs-toggle="tab" data-bs-target="#Showcase" type="button" role="tab">
                    Showcase
                </button>
                <button class="nav-link" id="Stamina-tab" data-bs-toggle="tab" data-bs-target="#Stamina" type="button" role="tab">
                    Stamina
                </button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane active" id="Account" role="tabpanel">
                {{ (new \App\Domain\Gs2Account\ServiceDomain())->namespacesView('account.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Chat" role="tabpanel">
                {{ (new \App\Domain\Gs2Chat\ServiceDomain())->namespacesView('chat.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Datastore" role="tabpanel">
                {{ (new \App\Domain\Gs2Datastore\ServiceDomain())->namespacesView('datastore.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Dictionary" role="tabpanel">
                {{ (new \App\Domain\Gs2Dictionary\ServiceDomain())->namespacesView('dictionary.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Exchange" role="tabpanel">
                {{ (new \App\Domain\Gs2Exchange\ServiceDomain())->namespacesView('exchange.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Experience" role="tabpanel">
                {{ (new \App\Domain\Gs2Experience\ServiceDomain())->namespacesView('experience.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Friend" role="tabpanel">
                {{ (new \App\Domain\Gs2Friend\ServiceDomain())->namespacesView('friend.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Inbox" role="tabpanel">
                {{ (new \App\Domain\Gs2Inbox\ServiceDomain())->namespacesView('inbox.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Inventory" role="tabpanel">
                {{ (new \App\Domain\Gs2Inventory\ServiceDomain())->namespacesView('inventory.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="JobQueue" role="tabpanel">
                {{ (new \App\Domain\Gs2JobQueue\ServiceDomain())->namespacesView('jobQueue.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Limit" role="tabpanel">
                {{ (new \App\Domain\Gs2Limit\ServiceDomain())->namespacesView('limit.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Lottery" role="tabpanel">
                {{ (new \App\Domain\Gs2Lottery\ServiceDomain())->namespacesView('lottery.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Matchmaking" role="tabpanel">
                {{ (new \App\Domain\Gs2Matchmaking\ServiceDomain())->namespacesView('matchmaking.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Mission" role="tabpanel">
                {{ (new \App\Domain\Gs2Mission\ServiceDomain())->namespacesView('mission.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Money" role="tabpanel">
                {{ (new \App\Domain\Gs2Money\ServiceDomain())->namespacesView('money.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Quest" role="tabpanel">
                {{ (new \App\Domain\Gs2Quest\ServiceDomain())->namespacesView('quest.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Ranking" role="tabpanel">
                {{ (new \App\Domain\Gs2Ranking\ServiceDomain())->namespacesView('ranking.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Realtime" role="tabpanel">
                {{ (new \App\Domain\Gs2Realtime\ServiceDomain())->namespacesView('realtime.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Schedule" role="tabpanel">
                {{ (new \App\Domain\Gs2Schedule\ServiceDomain())->namespacesView('schedule.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Script" role="tabpanel">
                {{ (new \App\Domain\Gs2Script\ServiceDomain())->namespacesView('script.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Showcase" role="tabpanel">
                {{ (new \App\Domain\Gs2Showcase\ServiceDomain())->namespacesView('showcase.components.namespace.list', request()->namespaceName) }}
            </div>
            <div class="tab-pane" id="Stamina" role="tabpanel">
                {{ (new \App\Domain\Gs2Stamina\ServiceDomain())->namespacesView('stamina.components.namespace.list', request()->namespaceName) }}
            </div>
        </div>
    </div>
@endsection
