@extends('layouts.guest')

@section('title', 'Réinitialisation du mot de passe')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 40px;">
            </div>

            <p class="text-muted small mb-4">
                Vous avez oublié votre mot de passe ? Aucun souci. Entrez votre adresse email ci-dessous et nous vous
                enverrons un lien de réinitialisation.
            </p>

            @if (session('success'))
                <x-forms.alert type="success">
                    {{ session('success') }}
                </x-forms.alert>
            @endif

            @if (session('error'))
                <x-forms.alert type="error">
                    {{ session('error') }}
                </x-forms.alert>
            @endif

            @if (session('status'))
                <x-forms.alert type="success">
                    {{ session('status') }}
                </x-forms.alert>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <x-inputs.email-input 
                    name="email" 
                    label="Adresse email"
                    placeholder="exemple@email.com"
                    value=""
                    icon="fa-envelope"
                    required
                />

                <!-- Submit -->
                <div class="d-flex justify-content-end">
                    <x-buttons.button-primary text="Envoyer le lien" />
                </div>
            </form>
        </div>
    </div>
@endsection
