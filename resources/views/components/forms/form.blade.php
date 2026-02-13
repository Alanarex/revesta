@props([
    'title' => '',
    'action' => '',
    'method' => 'POST',
    'formId' => 'appForm',
])

<div class="row">
    @if ($title)
        <div class="mb-4">
            <h4>{{ $title }}</h4>
        </div>
    @endif

    <form id="{{ $formId }}" action="{{ $action }}"
        method="{{ in_array($method, ['GET', 'POST']) ? $method : 'POST' }}">
        @csrf
        @if ($method !== 'GET' && $method !== 'POST')
            @method($method)
        @endif

        {{ $slot }}
    </form>
</div>
