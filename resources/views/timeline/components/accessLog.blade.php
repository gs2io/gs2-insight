@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url('/players/'),
            $timeline->userId => url('/players/' . $timeline->userId),
            __('messages.model.timelines') => url('/players/' . $timeline->userId),
            $timeline->transactionId => url('/players/' . $timeline->userId . '/timelines/' . $timeline->transactionId),
        ]
    ])
    <div class="p-8">
        <div class="bg-white shadow-sm rounded-lg p-5 mb-4 justify-between items-center">
            <div class="px-4 flex justify-between items-center">
                <div class="mb-4 whitespace-nowrap">
                    <p class="text-xl font-bold text-gray-500">{{ $event->action }}</p>
                </div>
                <div class="mb-4 whitespace-nowrap">
                    <p class="text-l font-bold text-gray-500">{{ $event->timestamp }}</p>
                </div>
            </div>
            <div class="px-4 flex justify-between items-center">
                <div class="mb-4 whitespace-nowrap">
                    <p class="font-bold text-gray-500">Request</p>
                </div>
            </div>
            <div class="px-8 mb-4 flex justify-between items-center">
                @include('commons.arguments', ['args' => json_decode($event->args, true)])
            </div>
            <div class="px-4 flex justify-between items-center">
                <div class="mb-4 whitespace-nowrap">
                    <p class="font-bold text-gray-500">Result</p>
                </div>
            </div>
            <div class="px-8 flex justify-between items-center">
                @include('commons.arguments', ['args' => json_decode($accessLog->result, true)])
            </div>
        </div>
    </div>
@endsection
