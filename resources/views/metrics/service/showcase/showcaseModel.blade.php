@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Showcase' => url('/metrics'),
            $showcaseModel->namespace->namespaceName => url("/metrics/showcase/{$showcaseModel->namespace->namespaceName}"),
            'Showcase Model' => url("/metrics/showcase/{$showcaseModel->namespace->namespaceName}"),
            $showcaseModel->showcaseModelName => url()->current(),
        ]
    ])
    {{ $showcaseModel->metricsView('showcase/components/namespace/showcaseModel/metrics') }}
@endsection
