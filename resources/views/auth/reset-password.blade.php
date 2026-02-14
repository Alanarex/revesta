@extends('layouts.guest')

@section('title', 'Nouveau mot de passe')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 80px;">
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <x-inputs.email-input 
                    name="email" 
                    label="Adresse email"
                    placeholder="exemple@email.com"
                    value="{{ $request->email }}"
                    icon="fa-envelope"
                    required
                />

                <x-inputs.password-input 
                    name="password" 
                    label="Nouveau mot de passe"
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

                <div class="d-flex justify-content-end">
                    <x-buttons.button-primary text="Réinitialiser" />
                </div>
            </form>
        </div>
    </div>
@endsection
