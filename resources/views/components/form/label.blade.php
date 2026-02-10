@props([
    'for' => '',
    'label' => '',
    'required' => false,
])

@if ($label)
    <label for="{{ $for }}" {{ $attributes->merge(['class' => 'form-label fw-bold']) }}>
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
            <span class="required-indicator sr-only">(required)</span>
        @endif
    </label>
@endif
