@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => "/",
            'Danger' => url()->current(),
        ]
    ])
    @foreach($namespaces as $namespace)
        <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
            <div class="flex justify-between items-center">
                <div class="flex flex-wrap">
                    <div class="px-6 py-4 whitespace-nowrap">
                        <p class="text-xl font-bold text-gray-500">{{ $namespace->namespaceName }}</p>
                    </div>
                </div>
                <div class="pr-4">
                    @if($permission == 'administrator')
                        <form action="{{ "/danger/gateway/{$namespace->namespaceName}/webSocketSession/disconnectAll" }}" method="post">
                            @csrf
                            <div class="px-6 py-2 whitespace-nowrap">
                                <div class="btn-group" role="group">
                                    <button id="takeOverDelete" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ __('messages.model.gateway.webSocketSession.action.disconnectAll') }}
                                    </button>
                                    <ul class="dropdown-menu p-0" style="min-width: 0" aria-labelledby="takeOverDelete">
                                        <li>
                                            <button type="submit" class="btn btn-outline-danger w-100">
                                                {{ __('messages.model.gateway.webSocketSession.action.accept') }}
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endsection
