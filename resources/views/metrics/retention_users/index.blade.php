<div class="bg-white p-6 shadow-sm rounded-lg justify-between items-center">
    <div id="test" class="w-full" style="height: 1000px">
    </div>
</div>
<script type="text/javascript">
    data = {
        "nodes": [
            {"name": " "},
            {"name": "Start"},
            @foreach($keys as $value)
            {"name": "{{ $value }}"},
            @endforeach
        ],
        "links": [
            @foreach($metrics as $value)
                @if(explode(':', $value->key)[0] === explode(':', $value->key)[1])
                    @if(explode(':', $value->key)[1] === $startYmd)
            {"source": "Start", "target": "{{ explode(':', $value->key)[1] }}", "value": {{ $value->value }} },
                    @else
            {"source": "{{ DateTime::createFromFormat('Y-m-d', explode(':', $value->key)[0])->sub(DateInterval::createFromDateString('1 days'))->format('Y-m-d') }}", "target": "{{ explode(':', $value->key)[1] }}", "value": {{ $value->value }} },
                    @endif
                @else
            {"source": "{{ explode(':', $value->key)[0] }}", "target": "{{ explode(':', $value->key)[1] }}", "value": {{ $value->value }} },
                @endif
            @endforeach
        ]
    }
    echarts.init(document.getElementById('test')).setOption({
        title: {
            text: 'Retention Diagram'
        },
        tooltip: {
            trigger: 'item',
            triggerOn: 'mousemove'
        },
        series: [
            {
                type: 'sankey',
                data: data.nodes,
                links: data.links,
                emphasis: {
                    focus: 'adjacency'
                },
                lineStyle: {
                    color: 'source',
                    curveness: 0.5
                }
            }
        ]
    });
</script>
