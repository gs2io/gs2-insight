@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url("/players"),
            $stamina->user->userId => url("/players/". $stamina->user->userId. "?mode=stamina"),
            "Stamina" => url("/players/". $stamina->user->userId. "?mode=stamina"),
            $stamina->user->namespace->namespaceName => url("/players/". $stamina->user->userId. "/stamina/". $stamina->user->namespace->namespaceName),
            "Stamina Model" => url("/players/". $stamina->user->userId. "/stamina/". $stamina->user->namespace->namespaceName),
            $stamina->staminaModelName => url()->current(),
        ]
    ])
    <div class="p-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="flex">
                @if($permission != 'null')
                <div class="w-25">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.current') }}
                        </label>
                        {{ $stamina->infoView('stamina/components/namespace/user/stamina/info') }}
                        <div class="p-2"></div>
                    </div>
                </div>
                @endif
                <div class="w-75">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.timelines') }}
                        </label>
                        {{ $stamina->timelineView('commons/timeline') }}
                        <div class="p-2"></div>
                    </div>
                </div>
            </div>
            <div class="p-2"></div>
        </div>
    </div>
@endsection
