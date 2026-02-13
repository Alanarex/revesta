@extends('layouts.guest')

@section('title', 'Confirmer le mot de passe')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ Vite::asset('resources/images/logo_large.svg') }}" alt="Logo" class="img-fluid"
                    style="max-height: 40px;">
            </div>

            <p class="text-muted small mb-4">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <x-inputs.password-input 
                    name="password" 
                    label="Mot de passe"
                    placeholder="Entrez votre mot de passe"
                    icon="fa-solid fa-lock"
                    required
                />

                <div class="d-flex justify-content-end mt-4">
                    <x-buttons.button-primary text="{{ __('Confirm') }}" />
                </div>
            </form>
        </div>
    </div>
@endsection
