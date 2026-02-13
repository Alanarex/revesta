@props([
    'label' => '',
    'name' => 'name',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'maxlength' => 255,
    'autocomplete' => false,
    'icon' => null,
    'clearBtn' => false,
    'clearBtnId' => '',
    'muted' => '',
    'readonly' => false,
])

<div class="mb-4">
    @if ($label)
        <x-form.label :for="$name" :label="$label" :required="$required" />
    @endif

    <div class="input-group">
        @if ($icon)
            <span class="input-group-text"><i class="{{ $icon }}"></i></span>
        @endif

        <input type="text" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}" maxlength="{{ $maxlength }}"
            autocomplete="{{ $autocomplete ? 'on' : 'off' }}" {{ $required ? 'required' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $attributes->merge(['class' => $clearBtn ? 'form-control pe-5' : 'form-control']) }}>

        @if ($clearBtn)
            <button type="button" id="{{ $clearBtnId }}" class="clear-btn">
                <i class="fa fa-x-lg"></i>
            </button>
        @endif

        @if ($muted)
            <small class="text-muted">{{ $muted }}</small>
        @endif

        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

    </div>

</div>

@pushOnce('styles')
    @vite('resources/scss/components/input.scss')
@endPushOnce
