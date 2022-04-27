@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Mission' => url('/metrics'),
            $missionGroupModel->namespace->namespaceName => url("/metrics/mission/{$missionGroupModel->namespace->namespaceName}"),
            'Mission Group Model' => url("/metrics/mission/{$missionGroupModel->namespace->namespaceName}"),
            $missionGroupModel->missionGroupModelName => url()->current(),
        ]
    ])
    {{ $missionGroupModel->metricsView('mission/components/namespace/missionGroupModel/metrics') }}
@endsection
