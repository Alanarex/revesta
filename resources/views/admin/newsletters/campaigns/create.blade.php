@extends('admin.newsletters.layouts')


@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Breadcrumbs -->
            <div class="mb-4">
                <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
            </div>

            <div class="mb-4">
                <h4>Créer une nouvelle campagne newsletter</h4>
            </div>

            <x-forms.form title="" :action="route('admin.newsletters.store')" method="POST" form-id="newsletterForm">
                <x-inputs.text-input name="title" label="Titre" placeholder="Entrez le titre de la campagne"
                    icon="fa-solid fa-heading" required />

                <x-inputs.quill-input name="content" label="Contenu" required />

                <div class="d-flex gap-2 justify-content-end pt-4">
                    <x-buttons.button-text :href="route('admin.newsletters.index')" text="Annuler" />
                    <x-buttons.button-secondary text="Sauvegarder le brouillon" name="action_draft" value="1" id="draftBtn" />
                    <x-buttons.button-outline text="Programmer" name="action_schedule" value="1" id="scheduleBtn" />
                    <x-buttons.button-warning text="Publier maintenant" name="action_publish_now" value="1" id="publishBtn" />
                    <x-buttons.button-primary text="Créer" name="action_create" value="1" id="createBtn" />
                </div>
            </x-forms.form>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/admin/newsletters/campaigns/form-handler.js')
@endpush
