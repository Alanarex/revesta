<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="mb-4">
            <h4>{{ isset($blog) ? 'Modifier le blog' : 'Créer un nouveau blog' }}</h4>
        </div>

        @php
            $isEdit = isset($blog);
            $action = $isEdit ? route('admin.blogs.update', $blog) : route('admin.blogs.store');
        @endphp

        <form id="blogForm" action="{{ $action }}" method="POST">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="mb-4">
                <label for="title" class="form-label fw-bold">Titre *</label>
                <input type="text" class="form-control border-0 border-bottom @error('title') is-invalid @enderror" 
                    id="title" name="title" value="{{ old('title', $blog->title ?? '') }}" 
                    required maxlength="255" placeholder="Entrez le titre du blog">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="short_description" class="form-label fw-bold">Description courte *</label>
                <textarea class="form-control border-0 border-bottom @error('short_description') is-invalid @enderror" 
                    id="short_description" name="short_description" rows="2" required maxlength="500" 
                    placeholder="Entrez une courte description">{{ old('short_description', $blog->short_description ?? '') }}</textarea>
                @error('short_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted d-block mt-2">Maximum 500 caractères</small>
            </div>

            <div class="mb-4">
                <label for="content" class="form-label fw-bold">Contenu *</label>
                <div id="editor" style="min-height: 300px;"></div>
                <input type="hidden" id="content" name="content"
                    value="{{ old('content', $blog->content ?? '') }}" required>
                @error('content')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2 justify-content-end pt-4">
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-link text-muted text-decoration-none">Annuler</a>
                <button type="submit" name="status" value="draft" class="btn btn-outline-secondary">Sauvegarder
                    le brouillon</button>
                <button type="submit" name="status"
                    value="{{ auth()->user()->isAdmin() ? 'published' : 'pending' }}"
                    class="btn btn-primary">Publier</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    @vite('resources/js/blogs/pages/form.js')
@endpush
