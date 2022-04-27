@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Script' => url('/metrics'),
            $scriptModel->namespace->namespaceName => url("/metrics/script/{$scriptModel->namespace->namespaceName}"),
            'Script' => url("/metrics/script/{$scriptModel->namespace->namespaceName}"),
            $scriptModel->scriptModelName => url()->current(),
        ]
    ])
    {{ $scriptModel->metricsView('script/components/namespace/scriptModel/metrics') }}
@endsection
