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
                    <p class="text-xl font-bold text-gray-500">{{ __('messages.action.' . str_replace('ByStampTask', '', str_replace('ByStampSheet', '', str_replace('ByUserId', '', $event->action)))) }}</p>
                </div>
                <div class="mb-4 whitespace-nowrap">
                    <p class="text-l font-bold text-gray-500">{{ $event->timestamp }}</p>
                </div>
            </div>
            <div class="px-4 justify-between items-center">
                <div class="mb-4 whitespace-nowrap">
                    <p class="font-bold text-gray-500">Consume</p>
                </div>
                @if(count(json_decode($issueStampSheetLog->tasks, true)) == 0)
                    <p class="px-4 mb-4 text-l font-bold text-gray-500">No Action</p>
                @endif
                @foreach(json_decode($issueStampSheetLog->tasks, true) as $task)
                    <div class="px-4 justify-between items-center">
                        <div class="mb-4 whitespace-nowrap">
                            <p class="text-l font-bold text-gray-500">{{ __('messages.action.' . str_replace('ByStampTask', '', str_replace('ByStampSheet', '', str_replace('ByUserId', '', json_decode($task, true)['action'])))) }}</p>
                        </div>
                        <div class="px-4 flex justify-between items-center">
                            @include('commons.arguments', ['args' => json_decode($task, true)['args']])
                        </div>
                        <div class="px-4 justify-between items-center">
                            <div class="mb-4 whitespace-nowrap">
                                <p class="mb-4 font-bold text-gray-500">Result</p>
                                <div class="px-4 mb-4 whitespace-nowrap">
                                    <div class="px-4 flex justify-between items-center">
                                        @if($executeStampTaskLogs[json_decode($task, true)['taskId']] == null)
                                            Not Execute(or disable GS2-Distributor logs)
                                        @else
                                            @include('commons.arguments', ['args' => json_decode($executeStampTaskLogs[json_decode($task, true)['taskId']]->result, true)])
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="py-4">
            </div>
            <div class="px-4 justify-between items-center">
                <div class="mb-4 whitespace-nowrap">
                    <p class="mb-4 font-bold text-gray-500">Acquire</p>
                    <div class="px-4 mb-4 whitespace-nowrap">
                        <p class="mb-4 text-l font-bold text-gray-500">{{ __('messages.action.' . str_replace('ByStampTask', '', str_replace('ByStampSheet', '', str_replace('ByUserId', '', $event->rewardAction)))) }}</p>
                        @if($event->rewardAction !== 'Void')
                            <div class="px-4 flex justify-between items-center">
                                @include('commons.arguments', ['args' => json_decode($event->rewardArgs, true)])
                            </div>
                        @endif
                    </div>
                </div>
                @if($event->rewardAction !== 'Void')
                    <div class="px-4 justify-between items-center">
                        <div class="mb-4 whitespace-nowrap">
                            <p class="mb-4 font-bold text-gray-500">Result</p>
                            <div class="px-4 mb-4 whitespace-nowrap">
                                <div class="px-4 flex justify-between items-center">
                                    @if($executeStampSheetLog == null)
                                        Not Execute(or disable GS2-Distributor logs)
                                    @else
                                        @include('commons.arguments', ['args' => json_decode($executeStampSheetLog->result, true)])
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
