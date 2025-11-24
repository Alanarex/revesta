<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <button id="sidebarToggle" class="btn btn-outline-secondary me-3" type="button">
            <i class="fa fa-bars"></i>
        </button>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                @auth
                <li class="nav-item me-3">
                    @php
                        $recentBookmarks = auth()->user()->blogBookmarks()->with('blog')->latest()->take(5)->get();
                        $bookmarksCount = auth()->user()->blogBookmarks()->count();
                    @endphp
                    <a class="nav-link text-dark position-relative" href="#" id="bookmarksDropdown" data-bs-toggle="dropdown">
                        <i class="fa fa-bookmark fs-5"></i>
                        @if($bookmarksCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" id="bookmarksBadge">
                                {{ $bookmarksCount }}
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                            <h6 class="mb-0">Signets</h6>
                            <a href="{{ route('bookmarks.index') }}" class="btn btn-sm btn-link">Voir tout</a>
                        </div>
                        <div>
                            @if($recentBookmarks->isEmpty())
                                <p class="text-center text-muted p-3">Aucun signet</p>
                            @else
                                @foreach($recentBookmarks as $rb)
                                    @if($rb->blog)
                                    <a href="{{ route('blogs.show', $rb->blog->slug ?? $rb->blog->id) }}" class="dropdown-item d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ Str::limit($rb->blog->title, 70) }}</div>
                                            <div class="small text-muted">{{ $rb->blog->user?->full_name ?? 'Auteur' }}</div>
                                        </div>
                                        <form method="POST" action="{{ route('bookmarks.destroy', $rb->id) }}" class="ms-3 mb-0 bookmark-remove-form-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-link text-danger bookmark-remove-inline" type="button" data-id="{{ $rb->id }}">✕</button>
                                        </form>
                                    </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </li>
                @endauth
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('profile.edit') }}"><i class="fa fa-user"></i> Profil</a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="nav-link text-dark border-0 bg-transparent" type="submit">
                            <i class="fa fa-sign-out-alt"></i> Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

@auth
<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.bookmark-remove-inline');
    if (!btn) return;
    e.preventDefault();
    if (!confirm('Retirer ce signet ?')) return;
    const id = btn.dataset.id;
    const url = '/bookmarks/' + id;

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    }).then(r => r.json()).then(data => {
        if (data && data.success) {
            // remove the parent anchor
            const anchor = btn.closest('a.dropdown-item');
            if (anchor) anchor.remove();
            // decrement badge
            const badge = document.getElementById('bookmarksBadge');
            if (badge) {
                let n = parseInt(badge.textContent || '0', 10) - 1;
                if (n <= 0) badge.remove(); else badge.textContent = n;
            }
        } else {
            alert(data.message || 'Impossible de retirer le signet');
        }
    }).catch(() => alert('Erreur réseau'));
});
</script>
@endauth
