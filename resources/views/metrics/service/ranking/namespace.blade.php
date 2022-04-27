@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Ranking' => url('/metrics'),
            $namespace->namespaceName => url()->current(),
        ]
    ])
    {{ $namespace->metricsView('ranking/components/namespace/metrics') }}
@endsection
