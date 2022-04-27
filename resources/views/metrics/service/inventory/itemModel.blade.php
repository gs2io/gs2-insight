@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.metrics') => url('/metrics'),
            'Inventory' => url('/metrics'),
            $itemModel->inventoryModel->namespace->namespaceName => url("/metrics/inventory/{$itemModel->inventoryModel->namespace->namespaceName}"),
            'Inventory Model' => url("/metrics/inventory/{$itemModel->inventoryModel->namespace->namespaceName}"),
            $itemModel->inventoryModel->inventoryModelName => url("/metrics/inventory/{$itemModel->inventoryModel->namespace->namespaceName}/inventoryModel/{$itemModel->inventoryModel->inventoryModelName}"),
            'Item Model' => url("/metrics/inventory/{$itemModel->inventoryModel->namespace->namespaceName}/inventoryModel/{$itemModel->inventoryModel->inventoryModelName}"),
            $itemModel->itemModelName => url()->current(),
        ]
    ])
    {{ $itemModel->metricsView('inventory/components/namespace/inventoryModel/itemModel/metrics') }}
@endsection
