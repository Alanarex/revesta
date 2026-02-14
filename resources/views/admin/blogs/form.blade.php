@props(['address' => null, 'action' => '', 'method' => 'POST'])

<div class="row">
    <div class="col-lg-10 mx-auto">

        <!-- Breadcrumbs -->
        <div class="mb-4">
            <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
        </div>

        <x-forms.form :title="$title" :action="$action" :method="$method" formId="blogForm">

            <x-inputs.text-input name="title" label="Titre" :value="$blog?->title"
                placeholder="Entrez le titre de votre article..." icon="fa-pen" required maxlength="255" />

            <x-inputs.textarea-input name="short_description" label="Description courte" :value="$blog?->short_description"
                placeholder="Écrivez une courte description attrayante..." icon="fa-align-left" rows="2" required
                maxlength="500" muted="Maximum 500 caractères" />

            <x-inputs.quill-input name="content" label="Contenu" :value="$blog?->content" required />

            <div class="d-flex gap-2 justify-content-end pt-4">
                <x-buttons.button-text text="Annuler" href="{{ route('admin.blogs.index') }}" class="text-muted" />
                <x-buttons.button-outline text="Sauvegarder le brouillon" type="submit" name="status"
                    value="draft" />
                <x-buttons.button-primary
                    text="{{ auth()->user()->isAdmin() ? 'Publier' : 'Soumettre pour approbation' }}" type="submit"
                    name="status" value="{{ auth()->user()->isAdmin() ? 'published' : 'pending' }}" />
            </div>

        </x-forms.form>
    </div>
</div>

@push('scripts')
    @vite('resources/js/blogs/pages/form.js')
@endpush
