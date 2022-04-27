@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url("/players"),
            $rate->user->userId => url("/players/". $rate->user->userId. "?mode=exchange"),
            "Exchange" => url("/players/". $rate->user->userId. "?mode=exchange"),
            $rate->user->namespace->namespaceName => url("/players/". $rate->user->userId. "/exchange/". $rate->user->namespace->namespaceName),
            "Rate Model" => url("/players/". $rate->user->userId. "/exchange/". $rate->user->namespace->namespaceName),
            $rate->rateModelName => url()->current(),
        ]
    ])
    <div class="p-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
                <div class="flex justify-between items-center">
                    <div class="flex flex-wrap">
                        <div class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xl font-bold text-gray-500">{{ $rate->rateModelName }}</p>
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
                        {{ $rate->currentAwaitsView('exchange/components/namespace/user/rate/await/current_list') }}
                        <div class="p-2"></div>
                    </div>
                </div>
                @endif
                <div class="w-75">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.timelines') }}
                        </label>
                        {{ $rate->timelineView('commons/timeline') }}
                        <div class="p-2"></div>
                    </div>
                </div>
            </div>
            <div class="p-2"></div>
        </div>
    </div>
@endsection
