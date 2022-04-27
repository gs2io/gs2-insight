@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Lottery' => url('/metrics'),
            $lotteryModel->namespace->namespaceName => url("/metrics/lottery/{$lotteryModel->namespace->namespaceName}"),
            'Lottery Model' => url("/metrics/lottery/{$lotteryModel->namespace->namespaceName}"),
            $lotteryModel->lotteryModelName => url()->current(),
        ]
    ])
    {{ $lotteryModel->metricsView('lottery/components/namespace/lotteryModel/metrics') }}
@endsection
