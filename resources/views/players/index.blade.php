@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url('/players'),
            $player->userId => url()->current(),
        ]
    ])
    <div class="p-8">
        {{ $player->infoView('players/components/info') }}
        <div class="p-2"></div>
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
                @if(isset(request()->mode) && request()->mode == 'account')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.account') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=account' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.account') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'quest')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.quest') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=quest' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.quest') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'inventory')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.inventory') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=inventory' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.inventory') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'experience')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.experience') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=experience' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.experience') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'money')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.money') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=money' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.money') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'mission')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.mission') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=mission' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.mission') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'stamina')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.stamina') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=stamina' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.stamina') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'dictionary')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.dictionary') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=dictionary' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.dictionary') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'inbox')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.inbox') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=inbox' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.inbox') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'datastore')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.datastore') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=datastore' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.datastore') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'friend')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.friend') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=friend' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.friend') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'ranking')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.ranking') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=ranking' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.ranking') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'chat')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.chat') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=chat' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.chat') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'exchange')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.exchange') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=exchange' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.exchange') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'showcase')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.showcase') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=showcase' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.showcase') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'lottery')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.lottery') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=lottery' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.lottery') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'limit')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.limit') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=limit' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.limit') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'jobQueue')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.jobQueue') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=jobQueue' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.jobQueue') }}
                    </a>
                @endif
                @if(isset(request()->mode) && request()->mode == 'gateway')
                    <button class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none text-blue-500 border-b-2 font-medium border-blue-500">
                        {{ __('messages.model.gateway') }}
                    </button>
                @else
                    <a href="{{ url()->current(). '?mode=gateway' }}" class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none">
                        {{ __('messages.model.gateway') }}
                    </a>
                @endif
            </nav>
            <div class="pt-4">
                @if(!isset(request()->mode) || request()->mode == 'timeline')
                    {{ $player->timelinesView(
                            'timeline/components/timelines',
                            (request()->beginAt ? DateTime::createFromFormat('Y-m-d\TH:i', request()->beginAt, new DateTimeZone($timezone)) : null),
                            (request()->endAt ? DateTime::createFromFormat('Y-m-d\TH:i', request()->endAt, new DateTimeZone($timezone)) : null),
                            request()->actions) }}
                @elseif(isset(request()->mode) && request()->mode == 'account')
                    {{ $player->account()->namespacesView('account/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'quest')
                    {{ $player->quest()->namespacesView('quest/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'inventory')
                    {{ $player->inventory()->namespacesView('inventory/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'experience')
                    {{ $player->experience()->namespacesView('experience/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'money')
                    {{ $player->money()->namespacesView('money/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'mission')
                    {{ $player->mission()->namespacesView('mission/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'stamina')
                    {{ $player->stamina()->namespacesView('stamina/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'dictionary')
                    {{ $player->dictionary()->namespacesView('dictionary/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'inbox')
                    {{ $player->inbox()->namespacesView('inbox/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'datastore')
                    {{ $player->datastore()->namespacesView('datastore/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'friend')
                    {{ $player->friend()->namespacesView('friend/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'ranking')
                    {{ $player->ranking()->namespacesView('ranking/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'chat')
                    {{ $player->chat()->namespacesView('chat/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'exchange')
                    {{ $player->exchange()->namespacesView('exchange/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'showcase')
                    {{ $player->showcase()->namespacesView('showcase/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'lottery')
                    {{ $player->lottery()->namespacesView('lottery/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'limit')
                    {{ $player->limit()->namespacesView('limit/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'jobQueue')
                    {{ $player->jobQueue()->namespacesView('jobQueue/components/namespace/flat_list') }}
                @elseif(isset(request()->mode) && request()->mode == 'gateway')
                    {{ $player->gateway()->namespacesView('gateway/components/namespace/flat_list') }}
                @endif
            </div>
        </div>
    </div>
@endsection
