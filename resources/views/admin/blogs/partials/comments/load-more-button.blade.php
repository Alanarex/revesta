@php
    $commentId = $commentId ?? null;
    $offset = $offset ?? 2;
    $level = $level ?? 1;
@endphp

@if ($commentId)
    <button class="btn btn-sm btn-link load-more-replies" data-comment-id="{{ $commentId }}"
        data-offset="{{ $offset }}" data-level="{{ $level }}">
        Voir plus de réponses...
    </button>
@endif
