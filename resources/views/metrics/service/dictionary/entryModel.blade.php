@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Dictionary' => url('/metrics'),
            $entryModel->namespace->namespaceName => url("/metrics/dictionary/{$entryModel->namespace->namespaceName}"),
            'Entry' => url("/metrics/dictionary/{$entryModel->namespace->namespaceName}"),
            $entryModel->entryModelName => url()->current(),
        ]
    ])
    {{ $entryModel->metricsView('dictionary/components/namespace/entryModel/metrics') }}
@endsection
