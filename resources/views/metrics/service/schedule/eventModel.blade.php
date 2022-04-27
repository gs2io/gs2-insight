@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Schedule' => url('/metrics'),
            $eventModel->namespace->namespaceName => url("/metrics/schedule/{$eventModel->namespace->namespaceName}"),
            'Event Model' => url("/metrics/schedule/{$eventModel->namespace->namespaceName}"),
            $eventModel->eventModelName => url()->current(),
        ]
    ])
    {{ $eventModel->metricsView('schedule/components/namespace/eventModel/metrics') }}
@endsection
