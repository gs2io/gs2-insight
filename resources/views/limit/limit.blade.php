@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url("/players"),
            $limit->user->userId => url("/players/". $limit->user->userId. "?mode=limit"),
            "Limit" => url("/players/". $limit->user->userId. "?mode=limit"),
            $limit->user->namespace->namespaceName => url("/players/". $limit->user->userId. "/limit/". $limit->user->namespace->namespaceName),
            "Limit Model" => url("/players/". $limit->user->userId. "/limit/". $limit->user->namespace->namespaceName),
            $limit->limitModelName => url()->current(),
        ]
    ])
    <div class="p-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
                <div class="flex justify-between items-center">
                    <div class="flex flex-wrap">
                        <div class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xl font-bold text-gray-500">{{ $limit->limitModelName }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex">
                @if($permission != 'null')
                <div class="w-25">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.current') }}
                        </label>
                        {{ $limit->currentCountersView('limit/components/namespace/user/limit/counter/current_list') }}
                        <div class="p-2"></div>
                    </div>
                </div>
                @endif
                <div class="w-75">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.timelines') }}
                        </label>
                        {{ $limit->timelineView('commons/timeline') }}
                        <div class="p-2"></div>
                    </div>
                </div>
            </div>
            <div class="m-4 bg-white shadow rounded-lg">
                <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.limit.counters') }}
                </label>
                {{ $limit->countersView('limit/components/namespace/user/limit/counter/list') }}
                <div class="p-2"></div>
            </div>
            <div class="p-2"></div>
        </div>
    </div>
@endsection
