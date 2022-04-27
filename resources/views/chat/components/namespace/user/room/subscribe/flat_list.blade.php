@foreach($subscribes as $subscribe)
    {{ $subscribe->infoView('chat/components/namespace/user/subscribe/info') }}
@endforeach
<div class="mb-4"></div>
{{ $subscribes->appends(request()->input())->links() }}

{{ $user->subscribeTimelineView('commons/timeline') }}

{{ $user->subscribeControllerView('chat/components/namespace/user/subscribe/controller') }}
