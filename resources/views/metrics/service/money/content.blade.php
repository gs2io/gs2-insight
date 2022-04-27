@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Money' => url('/metrics'),
            $content->namespace->namespaceName => url("/metrics/money/{$content->namespace->namespaceName}"),
            'Content' => url("/metrics/money/{$content->namespace->namespaceName}"),
            $content->contentId => url()->current(),
        ]
    ])
    {{ $content->metricsView('money/components/namespace/content/metrics') }}
@endsection
