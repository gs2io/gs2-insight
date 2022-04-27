<div class="bg-gray-100 shadow-sm rounded-lg">
    <div class="p-6 justify-between items-center">
        @include('timeline.components.search', [
            'url' => url()->current(),
        ])
    </div>
    <div class="bg-white p-6 justify-between items-center">
        <div class="flex">
            <div class="flex flex-col items-center mr-4">
                <div class="w-px h-1.5"></div>
                <div>
                    <div class="flex items-center justify-center w-5 h-5 bg-gray-300 rounded-full">
                    </div>
                </div>
                <div class="w-px h-full bg-gray-300"></div>
            </div>
            <div class="pb-4">
                <p class="mb-2 text-xl font-bold text-gray-300">{{ __('messages.model.players.components.timeline.now') }}</p>
            </div>
        </div>

        <div class="flex">
            <div class="flex flex-col items-center mr-4">
                <div class="h-full border border-dashed border-red-300"></div>
                <div>
                    <div class="flex items-center justify-center w-5 h-5 bg-red-300 rounded-full">
                    </div>
                </div>
                <div class="h-full border border-dashed border-red-300"></div>
            </div>
            <div class="pb-5 pt-7">
                <p class="mb-2 text-xl font-bold text-red-400">{{ __('messages.model.players.components.timeline.outOfScope') }}</p>
            </div>
        </div>

        <div class="flex">
            <div class="flex flex-col items-center mr-4">
                <div class="h-full border border-dashed border-blue-300"></div>
                <div>
                    <div class="flex items-center justify-center w-5 h-5 bg-blue-300 rounded-full">
                    </div>
                </div>
                <div class="h-full border border-dashed border-blue-300"></div>
            </div>
            <div class="pb-5 pt-7">
                <p class="mb-2 text-xl font-bold text-blue-400">{{ __('messages.model.players.components.timeline.outOfSearchScope') }}</p>
            </div>
        </div>

        <div id="search_result">
            <div id="result_timelines">
                @foreach($timelines as $timeline)
                    @if($timeline->type == 'access')
                        @include("timeline.components.access", ['timeline' => $timeline])
                    @elseif($timeline->type == 'issueStampSheet')
                        @include("timeline.components.action", ['timeline' => $timeline])
                    @endif
                @endforeach
                @if ($timelines->hasMorePages())
                    <p id="more" class="button"><a href="{{ $timelines->nextPageUrl() }}"></a></p>
                @endif
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-infinitescroll/2.1.0/jquery.infinitescroll.min.js"></script>
        <script type="text/javascript">
            $('#result_timelines').infinitescroll({
                    navSelector  : "#more",
                    nextSelector : "#more a",
                    itemSelector : ".timeline-content",
                    loading : {
                        img : '/img/loading.svg',
                        msgText : '',
                        finishedMsg : '',
                    },
                },
                function(_) {
                    $("#infscr-loading").remove();
                    $("#more").appendTo("#result_timelines");
                    $("#more").css({display: 'block'});
                },
            );

            $('#more a').click(
                function(){
                    $('#result_timelines').infinitescroll('retrieve');
                    return false;
                }
            );
        </script>

        <div class="flex">
            <div class="flex flex-col items-center mr-4">
                <div class="h-full border border-dashed border-blue-300"></div>
                <div>
                    <div class="flex items-center justify-center w-5 h-5 bg-blue-300 rounded-full">
                    </div>
                </div>
                <div class="h-full border border-dashed border-blue-300"></div>
            </div>
            <div class="pb-5 pt-7">
                <p class="mb-2 text-xl font-bold text-blue-400">{{ __('messages.model.players.components.timeline.outOfSearchScope') }}</p>
            </div>
        </div>

        <div class="flex">
            <div class="flex flex-col items-center mr-4">
                <div class="h-full border border-dashed border-red-300"></div>
                <div>
                    <div class="flex items-center justify-center w-5 h-5 bg-red-300 rounded-full">
                    </div>
                </div>
                <div class="h-full border border-dashed border-red-300"></div>
            </div>
            <div class="pb-5 pt-7">
                <p class="mb-2 text-xl font-bold text-red-400">{{ __('messages.model.players.components.timeline.outOfScope') }}</p>
            </div>
        </div>

        <div class="flex">
            <div class="flex flex-col items-center mr-4">
                <div class="w-px h-5 bg-gray-300"></div>
                <div>
                    <div class="flex items-center justify-center w-5 h-5 bg-gray-300 rounded-full">
                    </div>
                </div>
            </div>
            <p class="mt-4 text-xl font-bold text-gray-300">{{ __('messages.model.players.components.timeline.signUp') }}</p>
        </div>
    </div>
    <div class="p-1"></div>
</div>
