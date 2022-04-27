<div class="m-4 bg-white p-6 shadow-sm rounded-lg justify-between items-center">
    <nav>
        <div class="nav nav-tabs justify-content-end" id="nav-tab" role="tablist">
            <button class="nav-link active" id="hourly-tab" data-bs-toggle="tab" data-bs-target="#hourly" type="button" role="tab">
                Hourly
            </button>
            <button class="nav-link" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily" type="button" role="tab">
                Daily
            </button>
            <button class="nav-link" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly" type="button" role="tab">
                Weekly
            </button>
            <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab">
                Monthly
            </button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="hourly" role="tabpanel">
            @foreach($metrics['hourly'] as $metric)
                {{ $metric }}
            @endforeach
        </div>
        <div class="tab-pane" id="daily" role="tabpanel">
            @foreach($metrics['daily'] as $metric)
                {{ $metric }}
            @endforeach
        </div>
        <div class="tab-pane" id="weekly" role="tabpanel">
            @foreach($metrics['weekly'] as $metric)
                {{ $metric }}
            @endforeach
        </div>
        <div class="tab-pane" id="monthly" role="tabpanel">
            @foreach($metrics['monthly'] as $metric)
                {{ $metric }}
            @endforeach
        </div>
    </div>
</div>

<div class="p-6">
    {{ $namespace->rateModelsView("exchange/components/namespace/rateModel/list", request()->rateModelName) }}
</div>
