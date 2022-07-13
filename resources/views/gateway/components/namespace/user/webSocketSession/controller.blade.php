@if($permission != 'view')
<div class="p-4 bg-white shadow-sm rounded-lg ">
    @if($permission != 'operator')
    <form action="{{ "/players/{$user->userId}/gateway/{$user->namespace->namespaceName}/webSocketSession/disconnect" }}" method="post">
        @csrf
        <div class="px-6 py-2 whitespace-nowrap">
            <div class="btn-group" role="group">
                <button id="takeOverDelete" type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ __('messages.model.gateway.webSocketSession.action.disconnect') }}
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
@endif
