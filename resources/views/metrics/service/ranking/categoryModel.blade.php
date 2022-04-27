@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Ranking' => url('/metrics'),
            $categoryModel->namespace->namespaceName => url("/metrics/ranking/{$categoryModel->namespace->namespaceName}"),
            'Category Model' => url("/metrics/ranking/{$categoryModel->namespace->namespaceName}"),
            $categoryModel->categoryModelName => url()->current(),
        ]
    ])
    {{ $categoryModel->metricsView('ranking/components/namespace/categoryModel/metrics') }}
@endsection
