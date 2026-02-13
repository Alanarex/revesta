@extends('layouts.guest')

@section('title', 'Définir votre mot de passe')

@section('content')
    <div class="card border-0">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 40px;">
            </div>

            <h5 class="text-center mb-3">Définir votre mot de passe</h5>
            <p class="text-center text-muted small mb-4">Merci de creer un mot de passe pour acceder a votre compte.</p>

            @if ($errors->any())
                <x-forms.alert type="error" title="Erreurs de validation">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-forms.alert>
            @endif

            <form method="POST" action="{{ route('password.set.store', $user) }}">
                @csrf

                <x-inputs.password-input 
                    name="password" 
                    label="{{ __('Mot de passe') }}"
                    icon="fa-solid fa-lock"
                    placeholder="Entrez un mot de passe"
                    muted="Au moins 8 caracteres, incluant des majuscules, minuscules et chiffres."
                    required
                />

                <x-inputs.password-input 
                    name="password_confirmation" 
                    label="{{ __('Confirmer le mot de passe') }}"
                    icon="fa-solid fa-lock"
                    placeholder="Confirmez votre mot de passe"
                    required
                />

                <x-buttons.button-primary text="{{ __('Définir le mot de passe') }}" class="w-100" />
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">
                    Vous avez deja un compte?
                    <a href="{{ route('login') }}" class="text-decoration-none">Se connecter</a>
                </small>
            </div>
        </div>
    </div>
@endsection
