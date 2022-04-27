@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url("/players"),
            $item->inventory->user->userId => url("/players/". $item->inventory->user->userId. "?mode=inventory"),
            "Inventory" => url("/players/". $item->inventory->user->userId. "?mode=inventory"),
            $item->inventory->user->namespace->namespaceName => url("/players/". $item->inventory->user->userId. "/inventory/". $item->inventory->user->namespace->namespaceName),
            "Inventory Model" => url("/players/". $item->inventory->user->userId. "/inventory/". $item->inventory->user->namespace->namespaceName),
            $item->inventory->inventoryModelName => url("/players/". $item->inventory->user->userId. "/inventory/". $item->inventory->user->namespace->namespaceName. "/inventory/". $item->inventory->inventoryModelName),
            "Item Model" => url("/players/". $item->inventory->user->userId. "/inventory/". $item->inventory->user->namespace->namespaceName. "/inventory/". $item->inventory->inventoryModelName),
            $item->itemModelName => url()->current(),
        ]
    ])
    <div class="p-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
                <div class="flex justify-between items-center">
                    <div class="flex flex-wrap">
                        <div class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xl font-bold text-gray-500">{{ $item->itemModelName }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex">
                @if($permission != 'null')
                <div class="w-25">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.current') }}
                        </label>
                        {{ $item->infoView('inventory/components/namespace/user/inventory/item/info') }}
                        <div class="p-2"></div>
                    </div>
                </div>
                @endif
                <div class="w-75">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.timelines') }}
                        </label>
                        {{ $item->timelineView('commons/timeline') }}
                        <div class="p-2"></div>
                    </div>
                </div>
            </div>
            <div class="p-2"></div>
        </div>
    </div>
@endsection
