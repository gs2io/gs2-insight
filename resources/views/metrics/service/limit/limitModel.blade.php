@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Limit' => url('/metrics'),
            $limitModel->namespace->namespaceName => url("/metrics/limit/{$limitModel->namespace->namespaceName}"),
            'Limit Model' => url("/metrics/limit/{$limitModel->namespace->namespaceName}"),
            $limitModel->limitModelName => url()->current(),
        ]
    ])
    {{ $limitModel->metricsView('limit/components/namespace/limitModel/metrics') }}
@endsection
