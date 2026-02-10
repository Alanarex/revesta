@props(['text' => 'Button', 'type' => 'submit', 'name' => null, 'value' => null])

<button type="{{ $type }}" 
        @if($name) name="{{ $name }}" @endif
        @if($value) value="{{ $value }}" @endif
        {{ $attributes->merge(['class' => 'btn btn-outline-secondary']) }}>
    {{ $text }}
</button>
