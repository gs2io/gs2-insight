@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Script' => url('/metrics'),
            $namespace->namespaceName => url()->current(),
        ]
    ])
    {{ $namespace->metricsView('script/components/namespace/metrics') }}
@endsection
