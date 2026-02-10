@props([
    'type' => 'info',
    'icon' => null,
    'dismissible' => true,
    'title' => null,
])

@php
    $iconMap = [
        'success' => 'fa-regular fa-circle-check',
        'error' => 'fa-regular fa-circle-xmark',
        'danger' => 'fa-regular fa-circle-xmark',
        'warning' => 'fa-solid fa-triangle-exclamation',
        'info' => 'fa-solid fa-circle-info',
    ];
    
    $defaultIcon = $icon ?? ($iconMap[$type] ?? 'fa-solid fa-circle-info');
@endphp

<div {{ $attributes->merge(['class' => "alert alert-{$type}" . ($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">
    @if ($defaultIcon)
        <i class="fa {{ $defaultIcon }} me-2"></i>
    @endif

    @if ($title)
        <strong>{{ $title }}</strong>
    @endif

    {{ $slot }}

    @if ($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
