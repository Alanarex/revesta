@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-10 mx-auto">

            <x-forms.alert type="info" title="Information">
                Un email de verification sera envoye a l'utilisateur pour defini son mot de passe.
            </x-forms.alert>

            <x-layout.breadcrumbs :breadcrumbs="$breadcrumbs ?? []" />

            <x-forms.form formId="createUserForm" action="{{ route('admin.users.store') }}" method="POST"
                title="Creer un utilisateur">
                <div class="row">
                    <div class="col">
                        <x-inputs.text-input label="Prenom" name="first_name" value=""
                            icon="fa-user" placeholder="Entrez le prenom" :required="true" />
                    </div>
                    <div class="col">
                        <x-inputs.text-input label="Nom" name="last_name" value=""
                            icon="fa-user" placeholder="Entrez le nom" :required="true" />
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <x-inputs.email-input label="Email" name="email" value=""
                            icon="fa-envelope" placeholder="exemple@email.com" :required="true" />
                    </div>
                    <div class="col">
                        <x-inputs.text-input label="Telephone" name="phone" value=""
                            icon="fa-phone" placeholder="Numero de telephone" />
                    </div>
                </div>
                <x-inputs.select-input label="Role" name="role_id" value=""
                    icon="fa-user-shield" placeholder="Selectionner un role" :options="$rolesOptions ?? []" :required="true" />
                <div class="row">
                    <div class="col-md-6">
                        <x-inputs.select-input label="Statut Civil" name="civil_status" value=""
                            icon="fa-heart" placeholder="Selectionner le statut civil" :options="$civilStatuses ?? []" />
                    </div>
                    <div class="col-md-6">
                        <x-inputs.select-input label="Statut Familial" name="family_status"
                            value="" icon="fa-users"
                            placeholder="Selectionner le statut familial" :options="$familyStatuses ?? []" />
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end pt-3">
                    <x-buttons.button-text text="Annuler" type="button"
                        onclick="window.location='{{ route('admin.users.index') }}';" />
                    <x-buttons.button-primary text="Creer" />
                </div>
            </x-forms.form>
        </div>
    </div>
@endsection
