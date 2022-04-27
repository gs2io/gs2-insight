@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Mission' => url('/metrics'),
            $counterModel->namespace->namespaceName => url("/metrics/mission/{$counterModel->namespace->namespaceName}"),
            'Counter Model' => url("/metrics/mission/{$counterModel->namespace->namespaceName}"),
            $counterModel->counterModelName => url()->current(),
        ]
    ])
    {{ $counterModel->metricsView('mission/components/namespace/counterModel/metrics') }}
@endsection
