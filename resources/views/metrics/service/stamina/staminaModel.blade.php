@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Stamina' => url('/metrics'),
            $staminaModel->namespace->namespaceName => url("/metrics/stamina/{$staminaModel->namespace->namespaceName}"),
            'Stamina Model' => url("/metrics/stamina/{$staminaModel->namespace->namespaceName}"),
            $staminaModel->staminaModelName => url()->current(),
        ]
    ])
    {{ $staminaModel->metricsView('stamina/components/namespace/staminaModel/metrics') }}
@endsection
