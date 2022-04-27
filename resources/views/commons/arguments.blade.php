<div class="flex flex-wrap break-all">
    @if($args == null)
        {{ 'null' }}
    @elseif(is_bool($args))
            {{ $args ? 'true' : 'false' }}
    @elseif(is_array($args) && array_values($args) === $args && is_scalar($args[0]))
        {{ json_encode($args) }}
    @elseif(is_string($args) && json_decode($args, true) !== null && is_array(json_decode($args, true)))
        <div class="inline-flex flex-wrap py-2 px-2 border border-gray-300 rounded-md shadow-sm">
        @foreach(json_decode($args, true) as $key => $value)
            @if(!isset($ignoreFields) || !array_key_exists($key, $ignoreFields))
                @if($value != null)
                    <div class="p-4">
                        @if(!is_numeric($key))
                        <label class="block mb-2 text-sm font-medium text-gray-400">
                            {{ ucwords($key) }}
                        </label>
                        @endif
                        @include('commons.arguments', ['args' => $value, 'ignoreFields' => !isset($ignoreFields) ? [] : (array_key_exists($key, $ignoreFields) ? $ignoreFields[$key] : [])])
                    </div>
                @endif
            @endif
        @endforeach
        </div>
    @elseif(is_scalar($args))
        {{ $args }}
    @else
        <div class="inline-flex flex-wrap py-2 px-2 border border-gray-300 rounded-md shadow-sm">
        @foreach($args as $key => $value)
            @if(!isset($ignoreFields) || !array_key_exists($key, $ignoreFields))
                @if($value != null)
                    <div class="p-4">
                        @if(!is_numeric($key))
                        <label class="block mb-2 text-sm font-medium text-gray-400">
                            {{ ucwords($key) }}
                        </label>
                        @endif
                        @include('commons.arguments', ['args' => $value, 'ignoreFields' => !isset($ignoreFields) ? [] : (array_key_exists($key, $ignoreFields) ? $ignoreFields[$key] : [])])
                    </div>
                @endif
            @endif
        @endforeach
        </div>
    @endif
</div>
