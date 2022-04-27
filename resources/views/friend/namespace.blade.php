@extends('base')
@section('content')
    @include('commons.language')
    @include('commons.breadcrumb', [
        'hierarchy' => [
            'Home' => url('/'),
            __('messages.model.players') => url("/players"),
            request()->userId => url("/players/". request()->userId. "?mode=friend"),
            "Friend" => url("/players/". request()->userId. "?mode=friend"),
            $namespace->namespaceName => url()->current(),
        ]
    ])
    <div class="p-8">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="bg-gray-100 shadow-sm rounded-lg justify-between items-center">
                <div class="flex justify-between items-center">
                    <div class="flex flex-wrap">
                        <div class="px-6 py-4 whitespace-nowrap">
                            <p class="text-xl font-bold text-gray-500">{{ $namespace->namespaceName }}</p>
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
                        {{ $namespace->user(request()->userId)->currentFriendsView('friend/components/namespace/user/friend/current_list') }}
                        {{ $namespace->user(request()->userId)->currentFollowersView('friend/components/namespace/user/follower/current_list') }}
                        {{ $namespace->user(request()->userId)->profile()->infoView('friend/components/namespace/user/profile/info') }}
                        <div class="p-2"></div>
                    </div>
                </div>
                @endif
                <div class="w-75">
                    <div class="m-4 bg-white shadow rounded-lg">
                        <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                            {{ __('messages.model.timelines') }}
                        </label>
                        {{ $namespace->user(request()->userId)->timelineView('commons/timeline') }}
                        <div class="p-2"></div>
                    </div>
                </div>
            </div>
            <div class="m-4 bg-white shadow rounded-lg">
                <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.friend.friends') }}
                </label>
                {{ $namespace->user(request()->userId)->friendsView('friend/components/namespace/user/friend/list') }}
                <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.friend.followers') }}
                </label>
                {{ $namespace->user(request()->userId)->followersView('friend/components/namespace/user/follower/list') }}
                <label class="p-4 block mb-2 text-lg font-medium text-gray-900 dark:text-gray-300">
                    {{ __('messages.model.friend.profile') }}
                </label>
                {{ $namespace->user(request()->userId)->profile()->timelineView('commons/timeline') }}
                <div class="p-2"></div>
            </div>
            <div class="p-2"></div>
        </div>
    </div>
@endsection
