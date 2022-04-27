@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Mission' => url('/metrics'),
            $namespace->namespaceName => url()->current(),
        ]
    ])
    {{ $namespace->metricsView('mission/components/namespace/metrics') }}
@endsection
