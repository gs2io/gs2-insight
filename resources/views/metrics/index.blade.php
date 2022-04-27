<div class="p-6">
    <div id="{{ $category }}" class="w-full h-64">
    </div>
</div>
<script type="text/javascript">
    echarts.init(document.getElementById('{{ $category }}')).setOption({
        title: {
            text: '{{ preg_replace('/([^A-Z])([A-Z])/', "$1 $2", ucwords(substr($category, strpos($category, '_') + 1, strpos($category, '_', strpos($category, '_')+1)-strpos($category, '_')-1))) }} {{ strpos($category, '_count_') ? '(Count)' : (strpos($category, '_sum') ? '(Sum)' : '')  }}'
        },
        series: [
            @foreach($categories as $category)
            {
                name: '{{ $category }}',
                type: 'bar',
                stack: 'stack',
                emphasis: {
                    focus: 'series'
                },
                data: [
                    @foreach($keys as $key)
                    {{ $metrics[$key][$category] ?? 0 }},
                    @endforeach
                ],
            },
            @endforeach
        ],
        tooltip: {},
        legend: {
            show: false
        },
        xAxis: {
            type: 'category',
            data: [
                @foreach($keys as $key)
                '{{ $key }}',
                @endforeach
            ]
        },
        yAxis: {
            type: 'value'
        },
    });
</script>
