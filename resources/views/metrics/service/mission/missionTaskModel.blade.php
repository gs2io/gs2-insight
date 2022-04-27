@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Mission' => url('/metrics'),
            $missionTaskModel->missionGroupModel->namespace->namespaceName => url("/metrics/mission/{$missionTaskModel->missionGroupModel->namespace->namespaceName}"),
            'Mission Group Model' => url("/metrics/mission/{$missionTaskModel->missionGroupModel->namespace->namespaceName}"),
            $missionTaskModel->missionGroupModel->missionGroupModelName => url("/metrics/mission/{$missionTaskModel->missionGroupModel->namespace->namespaceName}/missionGroupModel/{$missionTaskModel->missionGroupModel->missionGroupModelName}"),
            'Mission Task Model' => url("/metrics/mission/{$missionTaskModel->missionGroupModel->namespace->namespaceName}/missionGroupModel/{$missionTaskModel->missionGroupModel->missionGroupModelName}"),
            $missionTaskModel->missionTaskModelName => url()->current(),
        ]
    ])
    {{ $missionTaskModel->metricsView('mission/components/namespace/missionGroupModel/missionTaskModel/metrics') }}
@endsection
