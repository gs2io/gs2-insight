@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Quest' => url('/metrics'),
            $questModel->questGroupModel->namespace->namespaceName => url("/metrics/quest/{$questModel->questGroupModel->namespace->namespaceName}"),
            'Quest Group Model' => url("/metrics/quest/{$questModel->questGroupModel->namespace->namespaceName}"),
            $questModel->questGroupModel->questGroupModelName => url("/metrics/quest/{$questModel->questGroupModel->namespace->namespaceName}/questGroupModel/{$questModel->questGroupModel->questGroupModelName}"),
            'Quest Model' => url("/metrics/quest/{$questModel->questGroupModel->namespace->namespaceName}/questGroupModel/{$questModel->questGroupModel->questGroupModelName}"),
            $questModel->questModelName => url()->current(),
        ]
    ])
    {{ $questModel->metricsView('quest/components/namespace/questGroupModel/questModel/metrics') }}
@endsection
