@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url("/players"),
            $counter->limit->user->userId => url("/players/". $counter->limit->user->userId. "?mode=limit"),
            "Limit" => url("/players/". $counter->limit->user->userId. "?mode=limit"),
            $counter->limit->user->namespace->namespaceName => url("/players/". $counter->limit->user->userId. "/limit/". $counter->limit->user->namespace->namespaceName),
            "Limit Model" => url("/players/". $counter->limit->user->userId. "/limit/". $counter->limit->user->namespace->namespaceName),
            $counter->limit->limitModelName => url("/players/". $counter->limit->user->userId. "/limit/". $counter->limit->user->namespace->namespaceName. "/limit/". $counter->limit->limitModelName),
            "Counter" => url("/players/". $counter->limit->user->userId. "/limit/". $counter->limit->user->namespace->namespaceName. "/limit/". $counter->limit->limitModelName),
            $counter->counterModelName => url()->current(),
        ]
    ])
    <div class="p-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
                <div class="flex justify-between items-center">
                    <div class="flex flex-wrap">
                        <div class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xl font-bold text-gray-500">{{ $counter->counterModelName }}</p>
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
                        {{ $counter->infoView('limit/components/namespace/user/limit/counter/info') }}
                        <div class="p-2"></div>
                    </div>
                </div>
                @endif
                <div class="w-75">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.timelines') }}
                        </label>
                        {{ $counter->timelineView('commons/timeline') }}
                        <div class="p-2"></div>
                    </div>
                </div>
            </div>
            <div class="p-2"></div>
        </div>
    </div>
@endsection
