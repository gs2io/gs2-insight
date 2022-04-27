@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Schedule' => url('/metrics'),
            $triggerModel->namespace->namespaceName => url("/metrics/schedule/{$triggerModel->namespace->namespaceName}"),
            'Trigger Model' => url("/metrics/schedule/{$triggerModel->namespace->namespaceName}"),
            $triggerModel->triggerModelName => url()->current(),
        ]
    ])
    {{ $triggerModel->metricsView('schedule/components/namespace/triggerModel/metrics') }}
@endsection
