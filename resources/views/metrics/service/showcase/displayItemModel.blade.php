@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Showcase' => url('/metrics'),
            $displayItemModel->showcaseModel->namespace->namespaceName => url("/metrics/showcase/{$displayItemModel->showcaseModel->namespace->namespaceName}"),
            'Showcase Model' => url("/metrics/showcase/{$displayItemModel->showcaseModel->namespace->namespaceName}"),
            $displayItemModel->showcaseModel->showcaseModelName => url("/metrics/showcase/{$displayItemModel->showcaseModel->namespace->namespaceName}/showcaseModel/{$displayItemModel->showcaseModel->showcaseModelName}"),
            'Display Item Model' => url("/metrics/showcase/{$displayItemModel->showcaseModel->namespace->namespaceName}/showcaseModel/{$displayItemModel->showcaseModel->showcaseModelName}"),
            $displayItemModel->displayItemId => url()->current(),
        ]
    ])
    {{ $displayItemModel->metricsView('showcase/components/namespace/showcaseModel/displayItemModel/metrics') }}
@endsection
