@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Chat' => url('/metrics'),
            $room->namespace->namespaceName => url("/metrics/chat/{$room->namespace->namespaceName}"),
            'Room' => url("/metrics/chat/{$room->namespace->namespaceName}"),
            $room->roomName => url()->current(),
        ]
    ])
    {{ $room->metricsView('chat/components/namespace/room/metrics') }}
@endsection
