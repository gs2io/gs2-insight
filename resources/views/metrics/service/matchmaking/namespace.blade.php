@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Matchmaking' => url('/metrics'),
            $namespace->namespaceName => url()->current(),
        ]
    ])
    {{ $namespace->metricsView('matchmaking/components/namespace/metrics') }}
@endsection
