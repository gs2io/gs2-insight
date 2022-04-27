@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Inventory' => url('/metrics'),
            $inventoryModel->namespace->namespaceName => url("/metrics/inventory/{$inventoryModel->namespace->namespaceName}"),
            'Inventory Model' => url("/metrics/inventory/{$inventoryModel->namespace->namespaceName}"),
            $inventoryModel->inventoryModelName => url()->current(),
        ]
    ])
    {{ $inventoryModel->metricsView('inventory/components/namespace/inventoryModel/metrics') }}
@endsection
