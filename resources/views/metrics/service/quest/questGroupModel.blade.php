@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Quest' => url('/metrics'),
            $questGroupModel->namespace->namespaceName => url("/metrics/quest/{$questGroupModel->namespace->namespaceName}"),
            'Quest Group Model' => url("/metrics/quest/{$questGroupModel->namespace->namespaceName}"),
            $questGroupModel->questGroupModelName => url()->current(),
        ]
    ])
    {{ $questGroupModel->metricsView('quest/components/namespace/questGroupModel/metrics') }}
@endsection
