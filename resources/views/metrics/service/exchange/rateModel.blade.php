@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Exchange' => url('/metrics'),
            $rateModel->namespace->namespaceName => url("/metrics/exchange/{$rateModel->namespace->namespaceName}"),
            'Rate' => url("/metrics/exchange/{$rateModel->namespace->namespaceName}"),
            $rateModel->rateModelName => url()->current(),
        ]
    ])
    {{ $rateModel->metricsView('exchange/components/namespace/rateModel/metrics') }}
@endsection
