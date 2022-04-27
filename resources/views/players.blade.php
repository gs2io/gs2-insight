@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url()->current(),
        ]
    ])
    <div class="p-8">
        @include('players.components.list', ['players' => $players])
    </div>
@endsection
