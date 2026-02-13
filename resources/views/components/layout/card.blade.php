@props([
    'class' => '',
    'headerClass' => '',
    'bodyClass' => '',
    'headerId' => '',
    'bodyId' => '',
])

@php
    $cardClass = trim('card bg-white rounded mb-3 ' . $class);
    $headerClass = trim('card-header ' . $headerClass);
    $bodyClass = trim('card-body ' . $bodyClass);
@endphp

<div {{ $attributes->merge(['class' => $cardClass]) }}>
    @isset($header)
        <div class="{{ $headerClass }}" @if ($headerId) id="{{ $headerId }}" @endif>
            {{ $header }}
        </div>
    @endisset

    <div class="{{ $bodyClass }}" @if ($bodyId) id="{{ $bodyId }}" @endif>
        {{ $slot }}
    </div>
</div>
