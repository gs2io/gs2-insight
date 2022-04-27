@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url()->current(),
        ]
    ])
    <div class="p-8">
        <div class="p-4 bg-white shadow-sm rounded-lg">
            <a href="{{ url('/players') }}" class="text-lg">{{ __('messages.model.players') }}</a>
            <p class="text-sm text-black-50">
                {{ __('messages.model.players.help') }}
            </p>
        </div>
        <div class="p-1"></div>
        <div class="p-4 bg-white shadow-sm rounded-lg">
            <a href="{{ url('/metrics') }}" class="text-lg">{{ __('messages.model.metrics') }}</a>
            <p class="text-sm text-black-50">
                {{ __('messages.model.metrics.help') }}
            </p>
        </div>
        <div class="p-1"></div>
        <div class="p-4 bg-white shadow-sm rounded-lg">
            <a href="{{ url('/gcp') }}" class="text-lg">{{ __('messages.model.setup') }}</a>
            <p class="text-sm text-black-50">
                {{ __('messages.model.setup.help') }}
            </p>
        </div>
    </div>
@endsection
