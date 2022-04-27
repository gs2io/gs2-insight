@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url("/players"),
            $wallet->user->userId => url("/players/". $wallet->user->userId. "?mode=money"),
            "Money" => url("/players/". $wallet->user->userId. "?mode=money"),
            $wallet->user->namespace->namespaceName => url("/players/". $wallet->user->userId. "/money/". $wallet->user->namespace->namespaceName),
            "Wallet" => url("/players/". $wallet->user->userId. "/money/". $wallet->user->namespace->namespaceName),
            $wallet->slot => url()->current(),
        ]
    ])
    <div class="p-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
                <div class="flex justify-between items-center">
                    <div class="flex flex-wrap">
                        <div class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xl font-bold text-gray-500">{{ $wallet->slot }}</p>
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
                        {{ $wallet->infoView('money/components/namespace/user/wallet/info') }}
                        <div class="p-2"></div>
                    </div>
                </div>
                @endif
                <div class="w-75">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.timelines') }}
                        </label>
                        {{ $wallet->timelineView('commons/timeline') }}
                        <div class="p-2"></div>
                    </div>
                    <div class="p-2"></div>
                </div>
            </div>
            <div class="p-2"></div>
        </div>
    </div>
@endsection
