@props(['href' => '#', 'text' => 'Link'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn btn-link text-muted text-decoration-none']) }}>
    {{ $text }}
</a>
