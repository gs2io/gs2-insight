@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url("/players"),
            $job->user->userId => url("/players/". $job->user->userId. "?mode=jobQueue"),
            "Job Queue" => url("/players/". $job->user->userId. "?mode=jobQueue"),
            $job->user->namespace->namespaceName => url("/players/". $job->user->userId. "/jobQueue/". $job->user->namespace->namespaceName),
            "Job" => url("/players/". $job->user->userId. "/jobQueue/". $job->user->namespace->namespaceName),
            $job->jobName => url()->current(),
        ]
    ])
    <div class="p-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="bg-white shadow-sm rounded-lg">
                <nav class="flex flex-wrap flex-col sm:flex-row">
                    @if(!isset(request()->mode) || request()->mode == 'timeline')
                        <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                            {{ __('messages.model.timelines') }}
                        </button>
                    @else
                        <a href="{{ url()->current(). '?mode=timeline' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                            {{ __('messages.model.timelines') }}
                        </a>
                    @endif
                    @if(isset(request()->mode) && request()->mode == 'status')
                        <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                            {{ __('messages.status') }}
                        </button>
                    @else
                        <a href="{{ url()->current(). '?mode=status' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                            {{ __('messages.status') }}
                        </a>
                @endif
                </nav>
            </div>
            <div class="pt-4">
                @if(!isset(request()->mode) || request()->mode == 'timeline')
                    <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                        {{ $job->jobName }}
                    </label>
                    {{ $job->timelineView('commons/timeline') }}
                @elseif(isset(request()->mode) && request()->mode == 'status')
                    {{ $job->infoView('jobQueue/components/namespace/user/job/info') }}
                @endif
            </div>
        </div>
    </div>
@endsection
