@extends('admin.newsletters.layouts')

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Breadcrumbs -->
            <div class="mb-4">
                <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />
            </div>

            <div class="mb-4">
                <h4>Modifier la campagne</h4>
            </div>

            <x-forms.form title="" :action="route('admin.newsletters.update', $campaign)" method="PUT" form-id="newsletterForm">
                <x-inputs.text-input name="title" label="Titre" :value="old('title', $campaign->title)" placeholder="Entrez le titre de la campagne"
                    icon="fa-solid fa-heading" required />

                <x-inputs.quill-input name="content" label="Contenu" :value="old('content', $campaign->content)" required />

                <div class="d-flex gap-2 justify-content-end pt-4">
                    <x-buttons.button-text :href="route('admin.newsletters.index')" text="Annuler" />
                    @if ($campaign->isDraft())
                        <x-buttons.button-secondary text="Sauvegarder le brouillon" name="action_draft" value="1" id="draftBtn" />
                        <x-buttons.button-outline text="Programmer" name="action_schedule" value="1" id="scheduleBtn" />
                        <x-buttons.button-warning text="Publier maintenant" name="action_publish_now" value="1" id="publishBtn" />
                    @endif
                    <x-buttons.button-primary text="Enregistrer" name="action_save" value="1" id="saveBtn" />
                </div>
            </x-forms.form>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/admin/newsletters/campaigns/form-handler.js')
@endpush
