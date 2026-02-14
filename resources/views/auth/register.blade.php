@extends('layouts.guest')

@section('title', 'Inscription')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 40px;">
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <x-inputs.text-input 
                    name="first_name" 
                    label="Prénom"
                    placeholder="Jean"
                    value=""
                    icon="fa-user"
                    required
                />

                <x-inputs.text-input 
                    name="last_name" 
                    label="Nom"
                    placeholder="Dupont"
                    value=""
                    icon="fa-user"
                    required
                />

                <x-inputs.email-input 
                    name="email" 
                    label="Email"
                    placeholder="exemple@email.com"
                    value=""
                    icon="fa-envelope"
                    required
                />

                <x-inputs.password-input 
                    name="password" 
                    label="Mot de passe"
                    placeholder="Au moins 8 caracteres"
                    icon="fa-lock"
                    required
                />

                <x-inputs.password-input 
                    name="password_confirmation" 
                    label="Confirmer le mot de passe"
                    placeholder="Confirmez votre mot de passe"
                    icon="fa-lock"
                    required
                />

                <!-- Footer Actions -->
                <div class="d-flex justify-content-between align-items-center">
                    <a class="text-decoration-none small" href="{{ route('login') }}">
                        Déjà inscrit ?
                    </a>

                    <x-buttons.button-primary text="S'inscrire" />
                </div>
            </form>
        </div>
    </div>
@endsection
