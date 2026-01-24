<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">{{ isset($blog) ? 'Modifier le blog' : 'Créer un nouveau blog' }}</h4>
            </div>
            <div class="card-body p-4">
                @php
                    $isEdit = isset($blog);
                    $action = $isEdit ? route('admin.blogs.update', $blog) : route('admin.blogs.store');
                @endphp

                <form id="blogForm" action="{{ $action }}" method="POST">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="title" class="form-label">Titre *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title', $blog->title ?? '') }}" required maxlength="255">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="short_description" class="form-label">Description courte *</label>
                        <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description"
                            name="short_description" rows="2" required maxlength="500">{{ old('short_description', $blog->short_description ?? '') }}</textarea>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maximum 500 caractères</small>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label">Contenu *</label>
                        <div id="editor" style="min-height: 300px;"></div>
                        <input type="hidden" id="content" name="content"
                            value="{{ old('content', $blog->content ?? '') }}" required>
                        @error('content')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('profile.edit', ['tab' => 'blogs']) }}" class="btn btn-secondary">Annuler</a>
                        <button type="submit" name="status" value="draft" class="btn btn-outline-primary">Sauvegarder
                            le brouillon</button>
                        <button type="submit" name="status"
                            value="{{ auth()->user()->isAdmin() ? 'published' : 'pending' }}"
                            class="btn btn-success">Publier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/blogs/pages/form.js')
@endpush
