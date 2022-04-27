@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Limit' => url('/metrics'),
            $namespace->namespaceName => url()->current(),
        ]
    ])
    {{ $namespace->metricsView('limit/components/namespace/metrics') }}
@endsection
