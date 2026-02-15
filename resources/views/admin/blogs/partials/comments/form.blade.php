@php
    $blogId = $blogId ?? null;
    $parentId = $parentId ?? '';
    $placeholder = $placeholder ?? 'Ajouter un commentaire...';
    $showAvatar = $showAvatar ?? true;
    $userInitials = $userInitials ?? (Auth::check() ? Auth::user()->initials : '');
@endphp

<div class="d-flex align-items-start mb-4">
    @if ($showAvatar)
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
            style="width: 40px; height: 40px; font-size: 16px; font-weight: bold;">
            {{ $userInitials }}
        </div>
    @endif
    <div class="flex-grow-1">
        <form class="comment-form" data-blog-id="{{ $blogId }}" data-parent-id="{{ $parentId }}">
            <div class="input-group">
                <input type="text" class="form-control comment-input" placeholder="{{ $placeholder }}" required>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
</div>
