@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Experience' => url('/metrics'),
            $experienceModel->namespace->namespaceName => url("/metrics/experience/{$experienceModel->namespace->namespaceName}"),
            'Experience Model' => url("/metrics/experience/{$experienceModel->namespace->namespaceName}"),
            $experienceModel->experienceModelName => url()->current(),
        ]
    ])
    {{ $experienceModel->metricsView('experience/components/namespace/experienceModel/metrics') }}
@endsection
